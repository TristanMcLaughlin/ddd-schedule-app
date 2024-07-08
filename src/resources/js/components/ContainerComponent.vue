<template>
    <div class="container">
        <div class="filters">
            <DatePicker
                :initial-start-date="startDate.format('YYYY-MM-DD')"
                :initial-end-date="endDate.format('YYYY-MM-DD')"
                @update-date-range="handleDateRangeUpdate"
            />
            <ProjectFilter :projects="projectsWithJobCodeNames" @project-filtered="filterProjects" />
        </div>
        <ProjectTable
            :projects="projectsWithJobCodeNames"
            :teams="teams"
            :date-range="dateRange"
            :bank-holidays="bankHolidays"
            :backlog-tickets="backlogTickets"
            :selected-project="selectedProject"
            @save-date-period="saveDatePeriod"
            v-if="loaded"
        />
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import ProjectTable from './ProjectTable.vue';
import DatePicker from './DatePicker.vue';
import ProjectFilter from "./ProjectFilter.vue";

export default {
    components: {
        ProjectFilter,
        ProjectTable,
        DatePicker,
    },
    data() {
        return {
            projects: [],
            teams: [],
            bankHolidays: [],
            backlogTickets: [],
            dateRange: [],
            loaded: false,
            startDate: moment(),
            endDate: moment().add(60, 'days'),
            selectedProject: null,
        };
    },
    async created() {
        await this.fetchData();
        await this.fetchBankHolidays();
        this.dateRange = this.generateDateRange();
        this.loaded = true;
    },
    computed: {
      projectsWithJobCodeNames() {
        return this.projects.map(p => ({...p, name: p.name.split('-')[0]}));
      },
    },
    methods: {
        async fetchData() {
            const response = await axios.post('/api/projects', {
                start_date: new moment(this.startDate).subtract(30, 'days').format('YYYY-MM-DD'),
                end_date: this.endDate.format('YYYY-MM-DD')
            });

            this.projects = Object.values(response.data.projects);
            this.teams = Object.values(response.data.teams);
            this.backlogTickets = Object.values(response.data.backlog_tickets);
        },
        async fetchBankHolidays() {
            const bankHolidays = await axios.get('/api/bank-holidays');
            this.bankHolidays = bankHolidays.data.map(bh => bh.date);
        },
        generateDateRange() {
            const start = moment(this.startDate);
            const end = moment(this.endDate);
            const range = [];

            while (start.isBefore(end)) {
                range.push(start.format('YYYY-MM-DD'));
                start.add(1, 'day');
            }

            return range;
        },
        async saveDatePeriod(payload) {
            try {
                const response = await axios.post(`/api/projects/${payload.project_id}/date-periods`, payload);
                this.fetchData();
            } catch (error) {
                console.error('Error saving date period:', error.response.data);
            }
        },
        handleDateRangeUpdate({ startDate, endDate }) {
            this.startDate = moment(startDate);
            this.endDate = moment(endDate);
            this.dateRange = this.generateDateRange();
        },
        filterProjects(selectedProject) {
            this.selectedProject = selectedProject;
        },
    },
};
</script>

<style lang="scss">
@import "../styles/app.scss";
body {
    font-family: "Lexend", sans-serif;
    font-size: 14px;
    line-height: 1.2;
    padding: 0;
    margin: 0;
    background: #353538;
}

h2 {
    font-weight: 600;
    font-size: 16px;
    margin-top: 10px;
    margin-bottom: 10px;
}

.container {
    margin: 0 auto;
    position: relative;
}

.filters {
    position: fixed;
    z-index: 2;
    display: flex;
    padding: 10px;
    border-bottom-right-radius: 20px;
    background: darkslategrey;
}
</style>
