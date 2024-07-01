<template>
    <div class="container">
        <ProjectTable
            :projects="projectsWithJobCodeNames"
            :teams="teams"
            :date-range="dateRange"
            @save-date-period="saveDatePeriod"
        />
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import ProjectTable from './ProjectTable.vue';

export default {
    components: {
        ProjectTable
    },
    data() {
        return {
            projects: [],
            teams: [],
            dateRange: this.generateDateRange(),
        };
    },
    created() {
        this.fetchData();
    },
    computed: {
      projectsWithJobCodeNames() {
        return this.projects.map(p => ({...p, name: p.name.split('-')[0]}));
      },
    },
    methods: {
        async fetchData() {
            const response = await axios.get('/api/projects');
            this.projects = Object.values(response.data.projects);
            this.teams = Object.values(response.data.teams);
        },
        generateDateRange() {
            const start = moment();
            const end = moment().add(45, 'days');
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
                console.log('Date period saved:', response.data);
                this.fetchData();
            } catch (error) {
                console.error('Error saving date period:', error.response.data);
            }
        },
    },
};
</script>

<style lang="scss">
/* Add some basic styling */
body {
    font-family: 'Arial', sans-serif;
    font-size: 12px;
}

.container {
    margin: 0 auto;
    padding: 50px;
}
</style>
