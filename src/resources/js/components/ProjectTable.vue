<template>
    <div>
        <div>
            <label for="projectFilter">Filter by Project Name:</label>
            <select v-model="selectedProject" id="projectFilter">
                <option value="">All</option>
                <option v-for="project in uniqueProjectNames" :key="project" :value="project">{{ project }}</option>
            </select>
        </div>

        <div>
            <table class="table">
                <thead>
                <tr>
                    <th>Assignee</th>
                    <th class="project-name">Project Name</th>
                    <th>CAM Link</th>
                    <th>RAG</th>
                    <th class="status">Status</th>
                    <th
                        v-for="date in dateRange"
                        :key="date"
                        class="rotated"
                        :class="{'is-weekend': isDateAWeekend(date)}"
                    ><span>{{ date }}</span></th>
                </tr>
                </thead>
                <tbody v-for="team in teams">
                <tr><td colspan="100%" class="table__team-name"><h2>{{team.name}}</h2></td></tr>
                <template v-for="assignee in team.assignees" :key="assignee.id">
                    <tr>
                        <td colspan="4" class="table__assignee"><strong>{{ assignee.name }}</strong>
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
                        <td></td>
                        <td class="project-name">{{ project.name }}</td>
                        <td><a :href="`https://opialtd.atlassian.net/browse/${project.id}`" target="_blank">{{ project.id }}</a></td>
                        <td>{{ project.rag_status }}</td>
                        <td class="status">{{ project.build_status }}</td>
                        <td v-for="date in dateRange" :key="date" :class="{
                            'highlighted': isDateInRange(date, project.date_periods, assignee.id),
                            'is-weekend': isDateAWeekend(date),
                            ...projectColourClass(project),
                            }"
                        ></td>
                    </tr>
                    <tr v-if="getBacklogTicketsForAssignee(assignee.id).length">
                        <td></td>
                        <td colspan="4" class="project-name">Backlog Tickets</td>
                        <td v-for="date in dateRange" :key="date" :class="{
                            'highlighted': isDateInBacklogRange(date, assignee.id),
                            'is-weekend': isDateAWeekend(date),
                            ...priorityColourClass(date, assignee.id)
                        }">
                            <span v-if="isDateInBacklogRange(date, assignee.id)" class="tooltip">
                                <img :src="getHighestPriorityBacklogTicket(date, assignee.id)?.icon" height="16"
                                     width="16" class="tooltip--img">
                                <span class="tooltiptext">
                                    <div v-for="ticket in getBacklogTicketsOnDate(date, assignee.id)" :key="ticket.id">
                                        <a :href="`https://opialtd.atlassian.net/browse${ticket.id}`" target="_blank">
                                            {{ ticket.summary }}
                                            ({{ ticket.priority }})
                                        </a>
                                    </div>
                                </span>
                            </span>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import AddDatePeriodWidget from './AddDatePeriodWidget.vue';
import Highest from '../../images/priorities/highest.svg';
import High from '../../images/priorities/high.svg';
import Medium from '../../images/priorities/medium.svg';
import Low from '../../images/priorities/low.svg';
import Lowest from '../../images/priorities/lowest.svg';

export default {
    components: {
        AddDatePeriodWidget,
    },
    props: ['projects', 'teams', 'dateRange', 'bankHolidays', 'backlogTickets'],
    data() {
        return {
            selectedProject: '',
            unavailableStatuses: ['abandoned', 'ended'],
            addingPeriod: null,
            priorityOrder: [
                {name: 'Highest', icon: Highest},
                {name: 'High', icon: High},
                {name: 'Medium', icon: Medium},
                {name: 'Low', icon: Low},
                {name: 'Lowest', icon: Lowest},
            ],
        };
    },
    computed: {
        uniqueProjectNames() {
            const projectNames = this.projects.map(project => project.name);
            return [...new Set(projectNames)];
        },
    },
    methods: {
        isDateInRange(date, datePeriods, assigneeId) {
            const current = moment(date);
            return datePeriods.some(period =>
                period.assignee_id === assigneeId &&
                current.isBetween(moment(period.start), moment(period.end), 'days', '[]')
            );
        },
        getProjectsForAssignee(assigneeId) {
            const today = moment().startOf('day');

            return this.projects.filter(project =>
                project.date_periods.some(period => period.assignee_id === assigneeId && moment(period.end).isSameOrAfter(today))
            );
        },
        getFilteredProjectsForAssignee(assigneeId) {
            return this.getProjectsForAssignee(assigneeId).filter(project =>
                !this.unavailableStatuses.includes(project.build_status.toLowerCase())  &&
                (this.selectedProject === '' || project.name === this.selectedProject)
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
        isDateInBacklogRange(date, assigneeId) {
            const current = moment(date);
            return this.backlogTickets.some(ticket =>
                ticket.assignee_id === assigneeId &&
                current.isBetween(moment(ticket.start_date), moment(ticket.end_date), 'days', '[]')
            );
        },
        getBacklogTicketsOnDate(date, assigneeId) {
            const current = moment(date);
            return this.backlogTickets.filter(ticket =>
                ticket.assignee_id === assigneeId &&
                current.isBetween(moment(ticket.start_date), moment(ticket.end_date), 'days', '[]')
            );
        },
        getHighestPriorityBacklogTicket(date, assigneeId) {
            const tickets = this.getBacklogTicketsOnDate(date, assigneeId);
            const priorityOrder = this.priorityOrder.map(p => p.name);
            const ticket = tickets.sort((a, b) => priorityOrder.indexOf(a.priority) - priorityOrder.indexOf(b.priority))[0];
            return ticket ? this.priorityOrder.find(p => p.name === ticket.priority) : null;
        },
        getBacklogTicketsForAssignee(assigneeId) {
            return this.backlogTickets.filter(ticket => ticket.assignee_id === assigneeId);
        },
        priorityColourClass(date, assigneeId) {
            const priority = this.getHighestPriorityBacklogTicket(date, assigneeId);
            if (!priority) return {};
            return { [`highlighted--${priority.name.toLowerCase()}`]: true };
        },
    },
};
</script>

<style lang="scss">
.table {
    width: 100%;
    border-collapse: collapse;

    .project-name, .status {
        min-width: 140px;
        color: grey;
    }

    &__assignee {
        text-align: left;
    }

    thead {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    &__team-name {
        background-color: darkslategrey;
        color: white;
        position: sticky;
        top: 100px;
        z-index: 1;
    }
}

th, td {
    border: 1px solid #eee;
    padding: 0;
    text-align: center;
    width: 18px;
    height: 18px;
}

th {
    background-color: #f9f9f9;
    font-size: 12px;
    min-width: 100px;
}

.project-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-align: left;
}

.highlighted {
    background-color: #4CAF50;

    &.highlighted {
        &--green {
            background-color: #75be51;
        }

        &--amber {
            background-color: #e5d58b;
        }

        &--red {
            background-color: #e1947e;
        }

        &--backlog {
            background-color: #b9dcff;
        }

        &--holidays {
            background-color: #c0c0c0;
        }

        &--highest {
            background-color: #ff0000; // Change color as needed
        }

        &--high {
            background-color: #ff4500; // Change color as needed
        }

        &--medium {
            background-color: #ff8c00; // Change color as needed
        }

        &--low {
            background-color: #ffd700; // Change color as needed
        }

        &--lowest {
            background-color: #9acd32; // Change color as needed
        }
    }
}

.is-weekend {
    background-color: #D3D3D3 !important;
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

.tooltip {
    position: relative;
    display: inline-block;

    a {
        color: white;
        text-decoration: underline;
    }

    &--img {
        filter:
            drop-shadow( 1px  0px 0px white)
            drop-shadow(-1px  0px 0px white)
            drop-shadow( 0px  1px 0px white)
            drop-shadow( 0px -1px 0px white);
    }

    .tooltiptext {
        visibility: hidden;
        width: 300px;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;
        position: absolute;
        z-index: 1;
        bottom: 100%;
        left: 50%;
        margin-left: -150px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    &:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }
}
</style>
