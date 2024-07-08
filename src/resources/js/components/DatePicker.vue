<template>
    <div>
        <label for="start-date">Start Date:</label>
        <input id="start-date" type="date" v-model="startDate" @change="updateDateRange" />

        <label for="end-date">End Date:</label>
        <input id="end-date" type="date" v-model="endDate" @change="updateDateRange" />
    </div>
</template>

<script>
export default {
    props: {
        initialStartDate: {
            type: String,
            required: true,
        },
        initialEndDate: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            startDate: this.initialStartDate,
            endDate: this.initialEndDate,
        };
    },
    methods: {
        updateDateRange() {
            this.$emit('update-date-range', {startDate: this.startDate, endDate: this.endDate});
        },
    },
};
</script>

<style scoped>
label {
    margin-right: 10px;
}

input {
    margin-bottom: 10px;
    margin-right: 20px;
}
</style>
