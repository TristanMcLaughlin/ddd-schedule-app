<template>
    <div>
        <table class="table">
            <thead>
            <tr>
                <th>Project Name</th>
                <th>CAM Link</th>
                <th>RAG</th>
                <th>Status</th>
                <th
                    v-for="date in dateRange"
                    :key="date"
                    class="rotated"
                    :class="{
                        'is-weekend': isDateAWeekend(date),
                        'is-today': isDateToday(date),
                    }"
                ><span>{{ date }}</span></th>
            </tr>
            </thead>
            <tbody v-for="team in teams">
            <tr v-if="!selectedProject"><td colspan="100%" class="table__team-name"><h2>{{team.name}}</h2></td></tr>
            <template v-for="assignee in getFilteredAssignees(team.assignees)" :key="assignee.id">
                <tr>
                    <td colspan="100%" class="table__assignee"><strong>{{ assignee.name }}</strong>
                        <button @click="toggleAddPeriod(assignee.id)" class="add-date-period__new">➕</button>
                    </td>
                </tr>
                <AddDatePeriodWidget
                    v-if="isAddingPeriod(assignee.id)"
                    :projects="projects"
                    :date-range="dateRange"
                    :assignee-id="assignee.id"
                    @save-date-period="handleSaveDatePeriod"
                    @cancel-adding-period="cancelAddingPeriod"
                />
                <tr v-for="project in getFilteredProjectsForAssignee(assignee.id)" :key="project.id">
                    <td class="project-name">{{ project.name }}</td>
                    <td><a :href="`https://opialtd.atlassian.net/browse/${project.id}`" target="_blank">{{ project.id }}</a></td>
                    <td>{{ project.rag_status }}</td>
                    <td class="status">{{ project.build_status }}</td>
                    <td v-for="date in dateRange" :key="date" class="table--date" :class="{
                        ...isDateInRange(date, project.date_periods, assignee.id),
                        'is-weekend': isDateAWeekend(date),
                        'is-today': isDateToday(date),
                        ...projectColourClass(project),
                        }"
                    ></td>
                </tr>
                <BacklogTicketsRow
                    :date-range="dateRange"
                    :backlog-tickets="getFilteredBacklogTicketsForAssignee(assignee.id)"
                    :bank-holidays="bankHolidays"
                />
            </template>
            </tbody>
        </table>
    </div>
</template>

<script>
import moment from 'moment';
import AddDatePeriodWidget from './AddDatePeriodWidget.vue';
import BacklogTicketsRow from './BacklogTicketsRow.vue';

export default {
    components: {
        AddDatePeriodWidget,
        BacklogTicketsRow,
    },
    props: ['projects', 'teams', 'dateRange', 'bankHolidays', 'backlogTickets', 'selectedProject'],
    data() {
        return {
            unavailableStatuses: ['abandoned', 'ended'],
            addingPeriod: null,
        };
    },
    methods: {
        isDateInRange(date, datePeriods, assigneeId) {
            const current = moment(date);
            const matchingPeriod = datePeriods.find(period =>
                period.assignee_id === assigneeId &&
                current.isBetween(moment(period.start), moment(period.end), 'days', '[]')
            );

            if (matchingPeriod) {
                const isStart = current.isSame(moment(matchingPeriod.start), 'day');
                const isEnd = current.isSame(moment(matchingPeriod.end), 'day');
                return {
                    highlighted: true,
                    'highlighted--start': isStart,
                    'highlighted--end': isEnd,
                };
            }

            return {};
        },
        getProjectsForAssignee(assigneeId) {
            const today = moment().startOf('day');
            return this.projects.filter(project =>
                project.date_periods.some(period => period.assignee_id === assigneeId && moment(period.end).isSameOrAfter(today))
            );
        },
        getFilteredProjectsForAssignee(assigneeId) {
            return this.getProjectsForAssignee(assigneeId).filter(project =>
                !this.unavailableStatuses.includes(project.build_status.toLowerCase()) &&
                (!this.selectedProject || project.name === this.selectedProject)
            );
        },
        toggleAddPeriod(assigneeId) {
            this.addingPeriod = this.addingPeriod === assigneeId ? null : assigneeId;
        },
        isAddingPeriod(assigneeId) {
            return this.addingPeriod === assigneeId;
        },
        cancelAddingPeriod() {
            return this.addingPeriod = null;
        },
        handleSaveDatePeriod(payload) {
            this.$emit('save-date-period', payload);
            this.toggleAddPeriod(payload.assignee_id);
        },
        isDateAWeekend(date) {
            const current = moment(date);
            return [6, 0].includes(current.day()) ||
                this.bankHolidays.includes(current.format('YYYY-MM-DD'));
        },
        isDateToday(date) {
            const current = moment();
            return current.format('YYYY-MM-DD') === date;
        },
        projectColourClass(project) {
            const map = {
                'green': 'highlighted--green',
                'amber': 'highlighted--amber',
                'red': 'highlighted--red',
                'Backlog': 'highlighted--backlog',
                'Holidays': 'highlighted--holidays',
            };

            if (['Backlog', 'Holidays'].includes(project.name)) {
                return map[project.name];
            }

            const key = Object.keys(map).find(key => (project.rag_status || '').toLowerCase().includes(key));
            return key ? { [map[key]]: true } : null;
        },
        getFilteredAssignees(assignees) {
            if (!this.selectedProject) return assignees;
            return assignees.filter(assignee => this.getFilteredProjectsForAssignee(assignee.id).length > 0 || this.getFilteredBacklogTicketsForAssignee(assignee.id).length > 0);
        },
        getBacklogTicketsForAssignee(assigneeId) {
            return this.backlogTickets.filter(ticket => ticket.assignee_id === assigneeId);
        },
        getFilteredBacklogTicketsForAssignee(assigneeId) {
            return this.getBacklogTicketsForAssignee(assigneeId).filter(ticket =>
                !this.selectedProject || ticket.summary.includes(this.selectedProject)
            );
        },
    },
};
</script>

<style lang="scss">
.table {
    width: 100%;
    border-collapse: collapse;

    .project-name, .status {
        min-width: 150px;
        color: grey;
    }

    &__assignee {
        text-align: left;
        background: #ECE9E6;  /* fallback for old browsers */
        background: -webkit-linear-gradient(to right, #FFFFFF, #ECE9E6);  /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to right, #FFFFFF, #ECE9E6); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
    }

    thead {
        position: sticky;
        top: 0;
        z-index: 1;
        background: white;
    }

    &__team-name {
        color: white;
        position: sticky;
        top: 100px;
        z-index: 1;
        background: linear-gradient(to right, #11998e, #38ef7d); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
        text-align: left;
    }

    &--date {
        padding-left: 18px;
        padding-top: 18px;
    }
}

th, td {
    border: 1px solid rgba(0, 0, 0, 0.1);
    text-align: center;
    padding-left: 10px;
}

th {
    background-color: #f9f9f9;
    font-size: 12px;
    min-width: 100px;
    vertical-align: bottom;
    padding-bottom: 10px;
    border-width: 0;
}

.project-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-align: left;
}

.is-today {
    background-color: rgba(0,188,120,0.3);
}

.highlighted {
    background-color: #16c557;

    &--start {
        border-top-left-radius: 100%;
        border-bottom-left-radius: 100%;
    }

    &--end {
        border-top-right-radius: 100%;
        border-bottom-right-radius: 100%;
    }

    &.highlighted {
        &--green {
            background-color: #16c557;
        }

        &--amber {
            background-color: #ffc94a;
        }

        &--red {
            background-color: #ff855e;
        }
    }
}

.is-weekend {
    background-color: #D3D3D3 !important;
    border-radius: 0;
}


.new-period-cell {
    background-color: #FFFFE0;
}

.highlighted-new-period {
    background-color: #FFD700;
}

.rotated {
    height: 90px;
    width: 16px;
    vertical-align: bottom;
    padding: 5px 8px;
    margin: 0;
    position: relative;
    min-width: 0;

    span {
        display: block;
        transform: rotate(-90deg);
        transform-origin: left top 0;
        white-space: nowrap;
        font-size: 12px;
        line-height: 16px;
        position: absolute;
        left: 0;
    }
}
</style>
