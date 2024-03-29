<template>
  <div v-if="can('visitor_view')">
    <VisitorRequestsList
      title="Visitors"
      :statsFilterValue="'all_approved'"
      :isDashboard="false"
      :defaultDates="getDefaultDates"
    />
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  computed: {
    getDefaultDates() {
      const currentDate = new Date();
      const firstDateOfMonth = new Date(
        currentDate.getFullYear(),
        currentDate.getMonth(),
        1
      );

      return [
        firstDateOfMonth.toISOString().slice(0, 10),
        currentDate.toISOString().slice(0, 10),
      ];
    },
  },
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
  },
};
</script>
