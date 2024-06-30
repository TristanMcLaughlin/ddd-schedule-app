<template>
    <tr>
        <td></td>
        <td>
            <select v-model="newDatePeriod.projectId">
                <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option>
            </select>
        </td>
        <td colspan="2">
        </td>
        <td v-for="date in dateRange" :key="date" class="new-period-cell" :class="getCellClass(date)"
            @mousedown="startDateSelection(date)"
            @mouseup="endDateSelection(date)"
            @mouseover="highlightDateSelection(date)"></td>
        <td>
            <button @click="saveDatePeriod">Save</button>
            <button @click="cancelDatePeriod">Cancel</button>
        </td>
    </tr>
</template>

<script>
import moment from 'moment';

export default {
    props: ['projects', 'dateRange', 'assigneeId'],
    data() {
        return {
            newDatePeriod: {
                projectId: '',
                startDate: '',
                endDate: '',
            },
            selecting: false,
        };
    },
    methods: {
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
        getCellClass(date) {
            if (this.isDateInNewPeriodRange(date)) {
                return 'highlighted-new-period';
            }
            return '';
        },
        saveDatePeriod() {
            const payload = {
                assignee_id: this.assigneeId,
                project_id: this.newDatePeriod.projectId,
                start_date: this.newDatePeriod.startDate,
                end_date: this.newDatePeriod.endDate,
            };
            this.$emit('save-date-period', payload);
        },
        cancelDatePeriod() {
            this.newDatePeriod = {
                projectId: '',
                startDate: '',
                endDate: '',
            };
            this.selecting = false;
            this.$emit('cancelAddingPeriod');
        },
    },
};
</script>

<style scoped>
    /* Add some basic styling */
    .new-period-cell {
        background-color: #FFFFE0; /* Light yellow for new period selection */
    }

    .highlighted-new-period {
        background-color: #FFD700; /* Darker yellow for currently highlighted period */
    }
</style>
