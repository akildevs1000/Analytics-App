<template>
  <v-row>
    <v-col cols="12" xs="12" sm="12" md="4" lg="4" xl="4">
      <AttendancePIE :page="page" />
    </v-col>
    <v-col cols="12" xs="12" sm="12" md="4" lg="4" xl="4">
      <AnnouncementNew />
    </v-col>
    <v-col cols="12" xs="12" sm="12" md="4" lg="4" xl="4">
      <DevicePIE />
    </v-col>
    <v-col cols="12">
      <RealTimeLog />
      <AttendanceLog />
      <Device />
      <ReportNotification />
    </v-col>
  </v-row>
</template>

<script>
import AttendancePIE from "./AttendancePIE.vue";
import AnnouncementNew from "./Announcement.vue";
import RealTimeLog from "./RealTimeLog.vue";
import DevicePIE from "./DevicePIE.vue";
import AttendanceLog from "./AttendanceLog.vue";
import Device from "./Device.vue";
import ReportNotification from "./ReportNotification.vue";

export default {
  props: ["page"],
  components: {
    RealTimeLog,
    AnnouncementNew,
    AttendanceLog,
    Device,
    ReportNotification,
    AttendancePIE,
    DevicePIE,
  },
};
</script>
