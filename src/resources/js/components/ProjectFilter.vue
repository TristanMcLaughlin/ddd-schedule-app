<template>
    <div>
        <v-select
            :options="projects"
            label="name"
            :reduce="project => project.name"
            v-model="selectedProject"
            :filterable="true"
            placeholder="Search for a project..."
        />
    </div>
</template>

<script>
import vSelect from "vue-select";

export default {
    props: ['projects'],
    data() {
        return {
            selectedProject: '',
        };
    },
    components: {
        vSelect,
    },
    watch: {
        selectedProject(value) {
            this.$emit('project-filtered', value ?? '');
        },
    },
};
</script>

<style scoped>
.v-select {
    width: 300px;
}
</style>
