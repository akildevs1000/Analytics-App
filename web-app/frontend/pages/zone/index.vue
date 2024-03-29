<template>
  <div v-if="can('zone_access')">
    <ZoneList />
  </div>
  <NoAccess v-else />
</template>
<script>
// import ZoneList from "../../components/Zone/list.vue";

export default {
  // components: { ZoneList },
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
  },
};
</script>
<style scoped src="@/assets/dashboard.css"></style>
