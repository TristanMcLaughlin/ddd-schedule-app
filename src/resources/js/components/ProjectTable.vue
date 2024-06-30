<template>
    <div>
        <div>
            <label for="projectFilter">Filter by Project Name:</label>
            <select v-model="selectedProject" id="projectFilter">
                <option value="">All</option>
                <option v-for="project in uniqueProjectNames" :key="project" :value="project">{{ project }}</option>
            </select>
        </div>
        <table>
            <thead>
            <tr>
                <th>Assignee</th>
                <th>Project Name</th>
                <th>RAG</th>
                <th>Status</th>
                <th v-for="date in dateRange" :key="date" class="rotated"><span>{{ date }}</span></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <template v-for="assignee in assignees" :key="assignee.id">
                <tr>
                    <td>{{ assignee.name }}</td>
                    <td colspan="4">
                        <button @click="toggleAddPeriod(assignee.id)">+</button>
                    </td>
                </tr>
                <tr v-for="project in getFilteredProjectsForAssignee(assignee.id)" :key="project.id">
                    <td></td>
                    <td class="project-name">{{ project.name }}</td>
                    <td>{{ project.rag_status }}</td>
                    <td>{{ project.build_status }}</td>
                    <td v-for="date in dateRange" :key="date" :class="{'highlighted': isDateInRange(date, project.date_periods, assignee.id)}"></td>
                </tr>
                <AddDatePeriodWidget
                    v-if="isAddingPeriod(assignee.id)"
                    :projects="projects"
                    :date-range="dateRange"
                    :assignee-id="assignee.id"
                    @save-date-period="handleSaveDatePeriod"
                    @cancel-adding-period="cancelAddingPeriod"
                />
            </template>
            </tbody>
        </table>
    </div>
</template>

<script>
import moment from 'moment';
import AddDatePeriodWidget from './AddDatePeriodWidget.vue';

export default {
    components: {
        AddDatePeriodWidget,
    },
    props: ['projects', 'assignees', 'dateRange'],
    data() {
        return {
            selectedProject: '',
            unavailableStatuses: ['abandoned', 'ended'],
            addingPeriod: null,
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
    },
};
</script>

<style>
/* Add some basic styling */
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #eee;
    padding: 0;
    text-align: center;
    width: 16px;
    height: 16px;
}

th {
    background-color: #f9f9f9;
    font-size: 12px;
    min-width: 100px;
}

.project-name {
    max-width: 100px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.highlighted {
    background-color: #4CAF50; /* Green background for highlighted cells */
}

.new-period-cell {
    background-color: #FFFFE0; /* Light yellow for new period selection */
}

.highlighted-new-period {
    background-color: #FFD700; /* Darker yellow for currently highlighted period */
}

.rotated {
    height: 120px;
    width: 16px;
    vertical-align: bottom;
    padding: 8px;
    margin: 0;
    position: relative;
    min-width: 0;
}

.rotated span {
    display: block;
    transform: rotate(-90deg);
    transform-origin: left top 0;
    white-space: nowrap;
    font-size: 12px;
    line-height: 16px;
    position: absolute;
    left: 0;
}
</style>
