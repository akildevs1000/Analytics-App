<template>
  <VisitorRequestsList
    title="Visitors"
    :statsFilterValue="'all_approved'"
    :isDashboard="false"
    :defaultDates="getDefaultDates"
  />
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
};
</script>
