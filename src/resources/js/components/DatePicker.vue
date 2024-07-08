<template>
    <div class="datepicker">
        <input id="start-date" type="date" v-model="startDate" @change="updateDateRange" /> -
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

<style lang="scss" scoped>
input {
    font-family: "Lexend", sans-serif;
    margin: 0 5px;
    padding: 5px;
    border: var(--vs-border-width) var(--vs-border-style) var(--vs-border-color);
    border-radius: var(--vs-border-radius);
}
</style>
