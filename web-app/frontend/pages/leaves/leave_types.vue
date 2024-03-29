<template>
  <div v-if="can('leave_type_access')">
    <v-tabs right>
      <v-tab> Leave Types </v-tab>
      <v-tab> Groups </v-tab>
      <v-tab-slider key="1" color="violet"></v-tab-slider>
      <v-tab-item>
        <Leavetype />
      </v-tab-item>
      <v-tab-item v-if="can('leave_group_access')">
        <Leavegroups />
      </v-tab-item>
    </v-tabs>
  </div>
  <NoAccess v-else />
</template>

<script>
import Leavetype from "../../components/Leaves/leavetype.vue";
import Leavegroups from "../../components/Leaves/leavegroups.vue";

export default {
  components: { Leavetype, Leavegroups },
  data: () => ({}),
  created() {},
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
  },
};
</script>
