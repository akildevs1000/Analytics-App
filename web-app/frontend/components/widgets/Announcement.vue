<template>
  <v-card class="mb-5 rounded-md" elevation="1">
    <v-toolbar class="rounded-md" color="background" dense flat dark>
      <v-toolbar-title
        ><span> {{ Model }} List </span></v-toolbar-title
      >
      <!-- <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            dense
            class="ma-0 px-0"
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
          >
            <v-icon color="white" class="ml-2" @click="getDataFromApi()" dark
              >mdi mdi-reload</v-icon
            >
          </v-btn>
        </template>
        <span>Reload</span>
      </v-tooltip> -->
      <v-spacer></v-spacer>
      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            to="/announcement"
            dense
            class="ma-0 px-0"
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
          >
            <v-icon color="white" class="ml-2" dark>mdi mdi-eye-outline</v-icon>
          </v-btn>
        </template>
        <span>Announcement List</span>
      </v-tooltip>
    </v-toolbar>
    <div :class="!dataLength ? `center-both` : ``" style="min-height: 300px">
      <ComonPreloader icon="notification" v-if="loading" />
      <div v-else-if="!loading && !dataLength">No record found</div>
      <div v-else style="width: 100%; max-height: 300px; overflow-y: auto">
        <v-card-text class="px-2" v-for="(announcement, i) in data" :key="i">
          <b>{{ announcement.title }}</b>
          <p>
            {{ announcement.description }}
            <small style="float: right; font-size: 9px"
              >Created At {{ announcement.dateTime }}</small
            >
          </p>

          <div
            v-if="i + 1 !== data.length"
            style="border-bottom: 1px solid #b3b1b1"
          ></div>
        </v-card-text>
      </div>
    </div>
  </v-card>
</template>
<script>
export default {
  data: () => ({
    options: {},
    Model: "Announcement",
    endpoint: "announcement_list",
    loading: false,
    dataLength: 0,

    headers: [
      {
        text: "Title",
        align: "left",
        sortable: true,
        key: "title",
        value: "title",
      },
      {
        text: "Start Date",
        align: "left",
        sortable: true,
        value: "start_date",
        key: "start_date",
      },
      {
        text: "End Date",
        align: "left",
        sortable: true,
        value: "end_date",
        key: "end_date",
      },
    ],

    data: [],
  }),

  created() {
    this.getDataFromApi();
  },

  methods: {
    getDataFromApi() {
      this.loading = true;
      let { sortBy, sortDesc, page } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      let options = {
        params: {
          page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: 5,
          company_id: this.$auth.user.company_id,
        },
      };

      this.$axios.get(this.endpoint, options).then(({ data }) => {
        this.loading = false;
        this.dataLength = data.total;
        this.data = data.data;
        if (!data.total) this.headers = [];
      });
    },
  },
};
</script>

<style scoped>
.center-both {
  height: 31vh; /* Adjust the height as needed */
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
