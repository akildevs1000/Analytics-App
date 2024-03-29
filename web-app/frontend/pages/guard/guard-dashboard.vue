<template>
  <div>
    <v-dialog v-model="dialogInformation" max-width="1300px">
      <v-card>
        <v-card-title class="popup_background">
          <span dense> Visitors Requests - {{ statisticsFilter }} </span>
          <v-spacer></v-spacer>
          <v-icon dark @click="dialogInformation = false" outlined>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <VisitorRequestsList
            :key="keyId"
            :isDashboard="true"
            :statsFilterValue="statisticsFilter"
          />
        </v-card-text>
      </v-card>
    </v-dialog>
    <div v-if="!loading">
      <v-dialog
        persistent
        v-model="dialogGeneralreport"
        :fullscreen="false"
        max-width="1600px"
      >
        <iframe
          v-if="iframeDisplay"
          :src="iframeUrl"
          frameborder="0"
          style="width: 100%; height: 600px"
        ></iframe>
      </v-dialog>
      <v-col lg="12" md="12" sm="12" xs="12">
        <v-row>
          <v-col md="12">
            <v-card class="pa-2" style="height: 354px; overflow: hidden">
              <v-row background fill>
                <v-col
                  lg="3"
                  md="3"
                  sm="3"
                  xs="3"
                  class="d-xs-flex"
                  style="flex: auto"
                >
                  <VisitorPieChart :items="items"></VisitorPieChart>
                </v-col>
                <v-col
                  lg="6"
                  md="6"
                  sm="6"
                  xs="6"
                  class="d-xs-flex pa-2 pt-5"
                  style="border-left: 1px solid #ddd"
                >
                  <v-row class="pa-10">
                    <v-col
                      cols="3"
                      class="card1 rounded-5"
                      v-for="(i, index) in items.visitorCounts"
                      :key="'v' + index"
                    >
                      <v-row @click="viewPopupInfo(i.title)">
                        <v-col cols="4" class="text-end">
                          <v-avatar size="30" :color="i.color">
                            <v-icon
                              size="20"
                              class="pa-2"
                              style="color: #fff"
                              >{{ i.icon }}</v-icon
                            >
                          </v-avatar>
                        </v-col>
                        <v-col class="text-left pa-0">
                          <div class="bold" style="font-size: 40px">
                            {{ i.value }}
                          </div>
                          {{ i.title }}
                        </v-col>
                      </v-row>
                    </v-col>
                  </v-row>

                  <v-row class="pa-10 pt-5">
                    <v-col
                      cols="3"
                      class="card1 rounded-5"
                      v-for="(i, index) in items.statusCounts"
                      :key="'v' + index"
                    >
                      <v-row @click="viewPopupInfo(i.title)">
                        <v-col cols="4" class="text-end">
                          <v-avatar size="30" :color="i.color">
                            <v-icon
                              size="20"
                              class="pa-1"
                              style="color: #fff"
                              >{{ i.icon }}</v-icon
                            >
                          </v-avatar>
                        </v-col>
                        <v-col class="text-left pa-0">
                          <div class="bold" style="font-size: 40px">
                            {{ i.value }}
                          </div>
                          {{ i.title }}
                        </v-col>
                      </v-row>
                    </v-col>
                  </v-row>
                </v-col>

                <v-col
                  lg="3"
                  md="3"
                  sm="3"
                  xs="3"
                  class="d-xs-flex"
                  style="border-left: 1px solid #ddd"
                >
                  <VisitorHourChart
                    :name="'visitor'"
                    :branch_id="null"
                    :height="300"
                  ></VisitorHourChart>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
        </v-row>
        <v-row>
          <v-col lg="12" md="12" sm="12" xs="12">
            <v-card
              class="py-2"
              style="height: 600px; overflow-x: hidden; overflow-y: scroll"
            >
              <VisitorLogs />
              <!-- <VisitorReport></VisitorReport> -->
            </v-card>
          </v-col>
        </v-row>
        <!-- <v-row>
          <v-col lg="12" md="12" sm="12" xs="12">
            <v-card class="py-2" style="overflow: hidden">
              <VisitorHourChart
                :name="'visitor'"
                :branch_id="null"
                :height="300"
              ></VisitorHourChart>
            </v-card>
          </v-col>
        </v-row> -->
      </v-col>
    </div>
    <Preloader v-else />
  </div>
</template>
<script>
import VisitorList from "../../components/Visitor/VisitorRequestsList.vue";
import VisitorReport from "../../components/Visitor/VisitorReport.vue";
import VisitorLogs from "../../components/Visitor/VisitorLogs.vue";
import VisitorHourChart from "../../components/Visitor/DashboardVisitorHourChart.vue";
import VisitorPieChart from "../../components/Visitor/DashboardVisitorPieChart.vue";
import VisitorRequestsList from "../../components/Visitor/VisitorRequestsList.vue";

export default {
  components: {
    VisitorList,
    VisitorPieChart,
    VisitorHourChart,
    VisitorReport,
    VisitorLogs,
    VisitorRequestsList,
  },

  data() {
    return {
      keyId: 1,
      counter: 1,
      loading: false,
      dialogGeneralreport: false,
      iframeDisplay: false,
      iframeUrl: "",
      items: [],
      filterTitle: "",
      branch_id: null,
      dialogInformation: false,
      statisticsFilter: "",
    };
  },
  created() {
    this.initialize();
  },
  mounted() {},
  computed: {
    getCurrentDate() {
      // Get the current date
      const date = new Date();

      // Get the year, month, and day from the date object
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");

      return `${year}-${month}-${day}`;
    },
  },
  filters: {
    get_decimal_value: function (value) {
      if (!value) return "";
      return (Math.round(value * 100) / 100).toFixed(2);
    },
    get_comma_seperator: function (x) {
      if (!x) return "";
      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },
  },
  methods: {
    viewPopupInfo(status) {
      this.statisticsFilter = status;

      console.log("this.statisticsFilter", this.statisticsFilter);
      this.keyId++;
      this.dialogInformation = true;
    },
    changeBranch1(branch_id) {
      console.log("branch_id", branch_id);
      this.branch_id = branch_id;

      // this.initialize();
    },
    filterStatus(status) {
      this.filterTitle = status;
      //this.counter++;
    },

    openDialog(announcement) {
      this.dialogData = announcement;
      this.dialog = true;
    },

    updateLink(url) {
      if (
        this.$axios.defaults.baseURL !=
        "https://stagingbackend.ideahrms.com/api"
      ) {
        url = url.replace(
          "https://stagingbackend.ideahrms.com/api",
          this.$axios.defaults.baseURL
        );
      }

      return url;
    },
    closeDialogGeneralreport() {
      this.iframeDisplay = false;
      this.dialogGeneralreport = false;
      //this.iframeUrl = "#";
    },
    showDialogGeneralreport(iframeUrl) {
      this.iframeDisplay = false;
      this.iframeUrl = this.updateLink(iframeUrl);
      this.dialogGeneralreport = true;

      this.iframeDisplay = true;
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    initialize() {
      this.loading = true;
      let options = {
        company_id: this.$auth.user.company_id,
        branch_id: this.branch_id > 0 ? this.branch_id : null,
      };

      this.$axios.get(`visitor-count`, { params: options }).then(({ data }) => {
        this.items = data;

        this.loading = false;

        // if (this.items.length > 0) {
        //   this.loading = false;
        // }
      });
    },
  },
};
</script>
<style scoped src="@/assets/dashboard.css"></style>
