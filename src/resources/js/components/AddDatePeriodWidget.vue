<template>
    <tr class="add-date-period">
        <td></td>
        <td>
            <v-select
                :options="projects"
                label="name"
                :reduce="project => project.id"
                v-model="newDatePeriod.projectId"
                :filterable="true"
                placeholder="Search for a project..."
            />
        </td>
        <td colspan="2">
        </td>
        <td v-for="date in dateRange" :key="date" class="new-period" :class="getCellClass(date)"
            @mousedown="startDateSelection(date)"
            @mouseup="endDateSelection(date)"
            @mouseover="highlightDateSelection(date)"></td>
        <td>
            <button @click="saveDatePeriod" class="add-date-period__save">💾</button>
            <button @click="cancelDatePeriod" class="add-date-period__cancel">❌</button>
        </td>
    </tr>
</template>

<script>
import moment from 'moment';
import vSelect from "vue-select";
import 'vue-select/dist/vue-select.css';

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
                return 'new-period--highlighted';
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
    components: {
        vSelect,
    },
};
</script>

<style  lang="scss">
    /* Add some basic styling */
    .new-period {
        background-color: #FFFFE0; /* Light yellow for new period selection */

        &--highlighted {
            background-color: #FFD700; /* Darker yellow for currently highlighted period */
        }
    }

    .add-date-period {
        --vs-actions-padding: 0 3px;
        --vs-font-size: 14px;
        --vs-line-height: 1;

        .vs__search, .vs__search:focus, .vs__selected {
            margin-top: 0px;
        }

        .vs__dropdown-toggle {
            padding: 0px;
        }

        &__save, &__cancel, &__new {
            background: none;
            border: 0;
            outline: 0;
            padding: 0 5px;
            font-size: 14px;
            height: 20px;
            line-height: 20px;
        }
    }
</style>
