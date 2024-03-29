<template>
  <div v-if="can('visitor_view')">
    <VisitorReport />
  </div>
  <NoAccess v-else />
</template>
<script>
import VisitorReport from "../../components/Visitor/VisitorReport.vue";

export default {
  components: { VisitorReport },

  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
  },
};
</script>
