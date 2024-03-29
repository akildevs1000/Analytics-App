<template>
  <div v-if="can('visitor_view')">
    <VisitorLogs />
  </div>
  <NoAccess v-else />
</template>
<script>
//import VisitorLogs from "../../components/Visitor/visitorlogs.vue";

export default {
  //components: { VisitorLogs },
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
  },
};
</script>
