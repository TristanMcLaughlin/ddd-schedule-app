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
                <tr v-if="isAddingPeriod(assignee.id)">
                    <td></td>
                    <td>
                        <select v-model="newDatePeriod.projectId">
                            <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option>
                        </select>
                    </td>
                    <td colspan="2">
                    </td>
                    <td v-for="date in dateRange" :key="date" class="new-period-cell" :class="{'new-period-cell-highlighted': isDateInNewPeriodRange(date)}"
                        @mousedown="startDateSelection(date)"
                        @mouseup="endDateSelection(date)"
                        @mouseover="highlightDateSelection(date)"></td>
                    <td>
                        <button @click="saveDatePeriod(assignee.id)">Save</button>
                    </td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';

export default {
    data() {
        return {
            projects: [],
            assignees: [],
            dateRange: this.generateDateRange(),
            selectedProject: '',
            unavailableStatuses: ['abandoned', 'ended'],
            addingPeriod: null,
            newDatePeriod: {
                projectId: '',
                startDate: '',
                endDate: '',
            },
            selecting: false,
        };
    },
    created() {
        this.fetchData();
    },
    computed: {
        uniqueProjectNames() {
            const projectNames = this.projects.map(project => project.name);
            return [...new Set(projectNames)];
        },
    },
    methods: {
        async fetchData() {
            const response = await axios.get('/api/projects');
            this.projects = Object.values(response.data.projects);
            this.assignees = Object.values(response.data.assignees);
        },
        generateDateRange() {
            const start = moment();
            const end = moment().add(30, 'days');
            const range = [];

            while (start.isBefore(end)) {
                range.push(start.format('YYYY-MM-DD'));
                start.add(1, 'day');
            }

            return range;
        },
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
            this.newDatePeriod = {
                projectId: '',
                startDate: '',
                endDate: '',
            };
        },
        isAddingPeriod(assigneeId) {
            return this.addingPeriod === assigneeId;
        },
        startDateSelection(date) {
            this.selecting = true;
            this.newDatePeriod.startDate = date;
            this.newDatePeriod.endDate = date;
        },
        endDateSelection(date) {
            this.selecting = false;
            this.newDatePeriod.endDate = date;
        },
        highlightDateSelection(date) {
            if (this.selecting) {
                this.newDatePeriod.endDate = date;
            }
        },
        isDateInNewPeriodRange(date) {
            if (!this.newDatePeriod.startDate || !this.newDatePeriod.endDate) return false;
            const current = moment(date);
            return current.isBetween(moment(this.newDatePeriod.startDate), moment(this.newDatePeriod.endDate), 'days', '[]');
        },
        async saveDatePeriod(assigneeId) {
            try {
                const payload = {
                    assignee_id: assigneeId,
                    project_id: this.newDatePeriod.projectId,
                    start_date: this.newDatePeriod.startDate,
                    end_date: this.newDatePeriod.endDate,
                };
                const response = await axios.post(`/api/projects/${payload.project_id}/date-periods`, payload);
                console.log('Date period saved:', response.data);
                this.fetchData();
                this.toggleAddPeriod(assigneeId);
            } catch (error) {
                console.error('Error saving date period:', error.response.data);
            }
        },
    },
};
</script>

<style>
/* Add some basic styling */
body {
    font-family: 'Arial', sans-serif;
    font-size: 12px;
}

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

.new-period-cell-highlighted {
    background-color: #4CAF50; /* Green background for highlighted cells */
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
