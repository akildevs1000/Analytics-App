<template>
  <div v-if="can('visitor_view')">
    <VisitorRequestsList
      :statsFilterValue="'Pending'"
      title="Visitor Requests"
      :isDashboard="false"
    />
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({}),
  created() {},
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
  },
};
</script>
