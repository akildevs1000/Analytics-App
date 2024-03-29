<template>
  <div v-if="can('announcement_access')">
    <AnnouncementType />
  </div>
  <NoAccess v-else />
</template>

<script>
import AnnouncementType from "../components/announcements/AnnouncementType.vue";

export default {
  components: { AnnouncementType },
  data: () => ({}),
  created() {},
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
  },
};
</script>
