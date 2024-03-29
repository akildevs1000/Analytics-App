<template>
  <div>
    <div v-if="item">
      <v-tabs class="slidegroup1" right>
        <v-tabs-slider color="violet"></v-tabs-slider>
        <v-tab>
          <span>Information </span>
        </v-tab>
        <v-tab @click="loadHistoryPage()">
          <span>History</span>
        </v-tab>
        <v-tab-item>
          <span class="bold"> Visitor </span>

          <span style="float: right">
            <span :style="'color:' + getRelatedColor(item)">{{
              item.status
            }}</span></span
          >
          <v-row class="100%" style="margin: auto; line-height: 36px">
            <v-col cols="4" style="padding: 0px">
              <v-img
                style="
                  border-radius: 50%;
                  width: 250px;
                  max-width: 95%;

                  height: 250px;
                  border: 1px solid #ddd;
                "
                :src="item.logo ? item.logo : '/no-profile-image.jpg'"
              >
              </v-img>
            </v-col>
            <v-col cols="4" style="padding-left: 5px; padding-top: 0px">
              <span cols="8">
                <b>{{ item.full_name || "---" }} </b></span
              >

              <div>
                <v-icon size="20" class="icon-blue" title="Date"
                  >mdi-calendar-range</v-icon
                >
                {{ item.from_date_display }}
                <span v-if="item.to_date_display != item.from_date_display">
                  to {{ item.to_date_display }}</span
                >
              </div>
              <div>
                <v-icon size="20" class="icon-blue" title="Time"
                  >mdi-clock-outline</v-icon
                >
                {{ item.time_in }} - {{ item.time_out }}
              </div>
              <div>
                <v-icon title="Purpose" size="20" class="icon-blue"
                  >mdi-briefcase-account-outline</v-icon
                >
                {{ item.purpose.name || "---" }}
              </div>
              <div>
                <v-icon size="20" class="icon-blue" title="Contact Number"
                  >mdi-cellphone</v-icon
                >
                {{ item.phone_number || "---" }}
              </div>

              <div class="bold" style="border-top: 1px solid #ddd">
                <v-icon size="20" color="green" title="Entry In Time"
                  >mdi-bank-transfer-in</v-icon
                >
                {{ (attendance && attendance.in) || "---" }} <br />
                {{ (attendance && attendance.device_in_name.name) || "---" }}
              </div>

              <div v-if="item.over_stay" style="color: red">
                Expected Out Time: {{ item.time_out }}
              </div>
            </v-col>
            <v-col cols="4" style="padding-left: 5px; padding-top: 0px">
              <div>&nbsp;</div>
              <div>
                <v-icon title="Company" size="30" class="icon-blue"
                  >mdi-domain</v-icon
                >
                {{ item.visitor_company_name || "---" }}
              </div>
              <span cols="8">
                <v-icon size="20" class="icon-blue" v-if="item.gender == 'Male'"
                  >mdi-human-male</v-icon
                >
                <v-icon
                  size="20"
                  class="icon-blue"
                  v-if="item.gender == 'Female'"
                  >mdi-human-female</v-icon
                >
                {{ item.gender || "---" }}
              </span>

              <div>
                <v-icon size="20" class="icon-blue" title="ID"
                  >mdi-identifier</v-icon
                >

                <span v-if="item.id_type == 1">Emirates ID</span>
                <span v-else-if="item.id_type == 2">National ID</span>

                : {{ item.id_number || "---" }}
              </div>

              <div>
                <v-icon size="20" class="icon-blue" title="Email"
                  >mdi-email</v-icon
                >
                {{ item.email || "---" }}
              </div>

              <div class="bold" style="border-top: 1px solid #ddd">
                <v-icon size="30" color="red" title="Exit Out Time"
                  >mdi-bank-transfer-out</v-icon
                >
                {{ (attendance && attendance.out) || "---" }}
                <br />
                {{ (attendance && attendance.device_out_name.name) || "---" }}

                <div style="color: red">
                  Overstay:
                  {{ (attendance && attendance.over_stay) || "---" }}

                  <!-- {{ verifyOverstay(item) }} -->
                </div>
              </div>
            </v-col>
          </v-row>
          <v-divider class="mt-3"></v-divider>
          <h4 style="background: #ddd" class="mb-3">Host :</h4>

          <v-row>
            <v-col col="4">
              <v-img
                style="
                  border-radius: 50%;
                  width: 200px;
                  max-width: 95%;
                  min-height: 100px;
                  height: auto;
                  border: 1px solid #ddd;
                "
                :src="
                  item.host
                    ? item.host.employee.profile_picture
                    : '/no-profile-image.jpg'
                "
              >
              </v-img>
            </v-col>

            <v-col cols="8">
              <v-row>
                <v-col col="3">Employee Name </v-col>
                <v-col col="9"
                  >: {{ item.host?.employee.first_name }}
                  {{ item.host?.employee.last_name }}</v-col
                >
              </v-row>
              <v-row>
                <v-col col="3">Contact Number</v-col>
                <v-col col="9">: {{ item.host?.employee.phone_number }}</v-col>
              </v-row>
              <v-row>
                <v-col col="3"> Email Id </v-col>
                <v-col col="9">
                  {{ item.host?.employee.user?.email || "---" }}
                </v-col>
              </v-row>
              <v-row>
                <v-col col="3"> Branch </v-col>
                <v-col col="9">
                  : {{ item.host?.employee.branch?.branch_name || "---" }}
                </v-col>
              </v-row>
              <v-row>
                <v-col col="3">Status </v-col>
                <v-col col="9">
                  <span :style="'color:' + getRelatedColor(item)">{{
                    item.status
                  }}</span></v-col
                >
              </v-row>
              <v-row>
                <v-col col="3">Reason:</v-col>
                <v-col col="9"> {{ item.reason || "---" }}</v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-tab-item>
        <v-tab-item>
          <v-card flat>
            <v-card-text>
              <VisitorHistory
                :visitorIdentificationNumber="item.id_number"
                :visitorFullName="item.full_name"
              ></VisitorHistory>
            </v-card-text>
          </v-card>
        </v-tab-item>
      </v-tabs>
    </div>
  </div>
</template>
<script>
import VisitorHistory from "../../components/Visitor/VisitorHistory.vue";
export default {
  props: ["item", "attendance"],
  components: { VisitorHistory },
  data: () => ({ keyid: 1 }),

  methods: {
    getRelatedColor(item) {
      let colors = {
        1: "purple",
        3: "red",
        2: "green",
        UNKNOWN: "purple",
      };
      return colors[item.status_id || "UNKNOWN"];
    },
    loadHistoryPage() {
      this.keyid++;
    },
    verifyOverstay(item) {
      if (item.attendances && item.attendances[0]) {
        if (item.status_id >= 6) {
          let inTime = item.attendances[0].in;
          let outTime = item.attendances[0].out;
          let overTimeInSeconds = 0;
          if (outTime == "" || outTime == null) {
            outTime = this.getCurrentTime();
          }

          if (inTime && outTime) {
            overTimeInSeconds =
              this.getTimeInSeconds(outTime) - this.getTimeInSeconds(inTime);

            if (overTimeInSeconds > 0) {
              return (
                "Over Stay : " + this.formatSecondsToTime(overTimeInSeconds)
              );
            }
          }
        }
      }
      return "";
    },
  },
};
</script>
