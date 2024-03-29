<template>
  <div v-if="can(`visitor_access`)">
    <div v-if="!loading">
      <v-dialog
        persistent
        v-model="dialogGeneralreport"
        :fullscreen="false"
        max-width="1200px"
      >
        <iframe
          v-if="iframeDisplay"
          :src="iframeUrl"
          frameborder="0"
          style="width: 100%; height: 600px"
        ></iframe>
      </v-dialog>
      <v-row>
        <v-col
          v-for="(i, index) in items.visitorCounts"
          :key="'v' + index"
          cols="12"
          md="3"
        >
          <div class="card p-2" :class="i.color" style="height: 150px">
            <div class="card-statistic-3">
              <div class="card-icon card-icon-large">
                <i :class="i.icon"></i>
              </div>
              <div class="card-content">
                <h3 class="card-title text-capitalize">{{ i.title }}</h3>
                <h2
                  class=""
                  style="cursor: pointer; font-size: 70px; text-align: center"
                  @click="filterStatus(i.title)"
                >
                  {{ i.value }}
                </h2>
                <!-- <p class="mb-0 text-sm">
                  <span
                    style="cursor: pointer"
                    class="handcursor font-11"
                    @click="showDialogGeneralreport(i.link)"
                  >
                    <span class="mr-2"> </span>
                    <span class="text-nowrap regular-font"></span>
                  </span>
                </p>
                <p class="mb-0 text-sm">
                  <span
                    style="cursor: pointer"
                    class="handcursor font-11"
                    @click="showDialogGeneralreport(i.multi_in_out)"
                  >
                    <span class="mr-2"> </span>
                    <span class="text-nowrap regular-font"></span>
                  </span>
                </p> -->
              </div>
            </div>
          </div>
        </v-col>
        <v-col
          v-for="(i, index) in items.statusCounts"
          :key="index"
          cols="12"
          md="3"
        >
          <div class="card p-2" :class="i.color" style="height: 150px">
            <div class="card-statistic-3">
              <div class="card-icon card-icon-large" style="">
                <i :class="i.icon"></i>
              </div>
              <div class="card-content">
                <h3 class="card-title text-capitalize">{{ i.title }}</h3>
                <h2
                  class=""
                  style="cursor: pointer; font-size: 70px; text-align: center"
                  @click="filterStatus(i.title)"
                >
                  {{ i.value }}
                </h2>
                <!-- <p class="mb-0 text-sm">
                  <span
                    style="cursor: pointer"
                    class="handcursor font-11"
                    @click="showDialogGeneralreport(i.link)"
                  >
                    <span class="mr-2"> </span>
                    <span class="text-nowrap regular-font"></span>
                  </span>
                </p>
                <p class="mb-0 text-sm">
                  <span
                    style="cursor: pointer"
                    class="handcursor font-11"
                    @click="showDialogGeneralreport(i.multi_in_out)"
                  >
                    <span class="mr-2"> </span>
                    <span class="text-nowrap regular-font"></span>
                  </span>
                </p> -->
              </div>
            </div>
          </div>
        </v-col>
      </v-row>

      <VisitorList :key="counter" :filterValue="filterTitle" />
    </div>
    <Preloader v-else />
  </div>
  <NoAccess v-else />
</template>
<script>
import VisitorList from "../../components/Visitor/VisitorRequestsList.vue";

export default {
  components: { VisitorList },

  data() {
    return {
      counter: 1,
      loading: false,
      dialogGeneralreport: false,
      iframeDisplay: false,
      iframeUrl: "",
      items: [],
      filterTitle: "",
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
