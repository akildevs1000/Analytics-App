<template>
  <div v-if="can('schedule_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-dialog persistent v-model="editDialog" max-width="35%">
      <v-card>
        <v-card-title dense class="primary white--text background">
          Edit Schedule
          <v-spacer></v-spacer>
          <v-icon @click="editDialog = false" outlined dark color="white">
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col md="6" mt-2 class="mt-2">
              <v-text-field
                :hide-details="true"
                v-model="editName"
                placeholder="Name"
                outlined
                dense
                :class="errors && errors.name ? 'mb-0' : 'mb-2'"
              ></v-text-field>
              <small
                style="font-size: 12px"
                v-if="errors && errors.name"
                class="error--text ma-0 pa-0"
                >{{ errors.name[0] }}</small
              >
            </v-col>
          </v-row>
          <table style="width: 100%">
            <tr style="font-size: 15px">
              <td style="width: 130px; text-align: center">
                <label class="col-form-label"><b>Day</b></label>
              </td>
              <td style="max-width: 150px; text-align: center">
                <label class="col-form-label"><b>Shifts</b></label>
              </td>
              <td style="text-align: center; width: 120px">
                <label class="col-form-label"><b>From</b></label>
              </td>
              <td style="text-align: center; width: 120px">
                <label class="col-form-label"><b>To</b></label>
              </td>
            </tr>
            <!-- <pre> {{ editShifts }}</pre> -->
            <tr
              v-for="(item, index) in editShifts"
              :key="index"
              style="text-align: center; font-size: 15px"
            >
              <td style="width: 80px; text-align: center">
                <label class="col-form-label"
                  ><b>{{ item.day }}</b></label
                >
              </td>
              <td
                style="width: 400px; text-align: left; padding: 8px 0px 0px 0px"
              >
                <v-select
                  class="mx-5 py-0"
                  :items="shifts"
                  dense
                  outlined
                  item-text="name"
                  item-value="id"
                  placeholder="Select"
                  :hide-details="true"
                  v-model="item.shift_id"
                >
                </v-select>
                <small
                  class="error--text text-left py-0 my-0"
                  v-if="!shift[index] && errors && errors.shift_ids"
                  style="margin-left: 20px; font-size: 12px"
                >
                  {{ errors.shift_ids[0] }}
                </small>
              </td>
              <td style="max-width: 150px; text-align: center">
                <label class="col-form-label">
                  {{ getFrom(item.shift_id) }}
                </label>
              </td>
              <td style="max-width: 150px; text-align: center">
                <label class="col-form-label">{{ getTo(item.shift_id) }}</label>
              </td>
            </tr>
          </table>

          <v-card-actions class="mr-1">
            <v-spacer></v-spacer>
            <!-- <v-btn class="error" small @click="close"> Cancel </v-btn> -->
            <v-btn class="primary" small @click="update_schedule">Update</v-btn>
          </v-card-actions>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog persistent v-model="dialog" max-width="35%">
      <v-card>
        <v-card-title dense class="primary white--text background">
          {{ formTitle }} Schedule
          <v-spacer></v-spacer>
          <v-icon @click="dialog = false" outlined dark color="white">
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-card-title>
              <v-row>
                <v-col md="6">
                  <v-text-field
                    :hide-details="true"
                    v-model="name"
                    placeholder="Name"
                    outlined
                    dense
                    :class="errors && errors.name ? 'mb-0' : 'mb-2'"
                  ></v-text-field>
                  <small
                    style="font-size: 12px"
                    v-if="errors && errors.name"
                    class="error--text ma-0 pa-0"
                    >{{ errors.name[0] }}</small
                  >
                </v-col>
              </v-row>
              <table style="width: 100%">
                <tr style="font-size: 15px">
                  <td style="width: 130px; text-align: center">
                    <label class="col-form-label"><b>Day</b></label>
                  </td>
                  <td style="max-width: 150px; text-align: center">
                    <label class="col-form-label"><b>Shifts</b></label>
                  </td>
                  <td style="text-align: center; width: 120px">
                    <label class="col-form-label"><b>From</b></label>
                  </td>
                  <td style="text-align: center; width: 120px">
                    <label class="col-form-label"><b>To</b></label>
                  </td>
                </tr>
                <tr
                  v-for="(item, index) in days"
                  :key="index"
                  style="text-align: center; font-size: 15px"
                >
                  <td style="width: 80px; text-align: center">
                    <label class="col-form-label"
                      ><b>{{ item }}</b></label
                    >
                  </td>
                  <td
                    style="
                      width: 400px;
                      text-align: left;
                      padding: 8px 0px 0px 0px;
                    "
                  >
                    <!-- {{ shift }}
                    {{ shift[index] }} -->
                    <v-select
                      class="mx-5 py-0"
                      :items="shifts"
                      dense
                      outlined
                      item-text="name"
                      item-value="id"
                      v-model="shift[index]"
                      placeholder="Select"
                      :hide-details="true"
                      @change="getTimeRange(shift[index])"
                    >
                    </v-select>
                    <small
                      class="error--text text-left py-0 my-0"
                      v-if="!shift[index] && errors && errors.shift_ids"
                      style="margin-left: 20px; font-size: 12px"
                    >
                      {{ errors.shift_ids[0] }}
                    </small>
                  </td>
                  <td style="max-width: 150px; text-align: center">
                    <label class="col-form-label">
                      {{ getFrom(shift[index]) }}
                    </label>
                  </td>
                  <td style="max-width: 150px; text-align: center">
                    <label class="col-form-label">{{
                      getTo(shift[index])
                    }}</label>
                  </td>
                </tr>
              </table>
            </v-card-title>
            <v-card-actions class="mr-1">
              <v-spacer></v-spacer>
              <!-- <v-btn class="error" small @click="close"> Cancel </v-btn> -->
              <v-btn class="primary" small @click="save">Save</v-btn>
            </v-card-actions>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>

    <div v-if="!preloader">
      <Back class="primary white--text" />

      <v-card elevation="0" class="mt-2" v-if="can(`shift_view`)">
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <v-toolbar-title><span> Schedule List</span></v-toolbar-title>
          <v-tooltip top color="primary">
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
                <v-icon color="white" class="ml-2" @click="get_schedule" dark
                  >mdi mdi-reload</v-icon
                >
              </v-btn>
            </template>
            <span>Reload</span>
          </v-tooltip>
          <v-spacer></v-spacer>
          <v-tooltip top color="primary">
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
                <v-icon color="white" class="ml-2" @click="dialog = true" dark
                  >mdi mdi-plus-circle</v-icon
                >
              </v-btn>
            </template>
            <span>Add New Schedule</span>
          </v-tooltip>
        </v-toolbar>
        <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
          {{ snackText }}

          <template v-slot:action="{ attrs }">
            <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
          </template>
        </v-snackbar>
        <v-data-table
          dense
          :headers="headers_table"
          :items="scheduleData"
          model-value="data.id"
          :loading="loading"
          :options.sync="options"
          :footer-props="{
            itemsPerPageOptions: [10, 50, 100, 500, 1000],
          }"
          class="elevation-1"
        >
          <template v-slot:item.sno="{ item, index }">
            <b>{{ ++index }}</b>
          </template>
          <template v-slot:item.name="{ item }">
            {{ item.name }}
          </template>
          <template v-slot:item.description="{ item }">
            <v-chip
              small
              class="primary ma-1"
              v-for="(j, s) in item.json"
              :key="s"
            >
              {{ j.day }} {{ j.time }} {{ getShiftType(j.shift_id) }}
            </v-chip>
          </template>
          <template v-slot:item.options="{ item }">
            <v-menu bottom left>
              <template v-slot:activator="{ on, attrs }">
                <div class="text-center">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </div>
              </template>
              <v-list width="120" dense>
                <v-list-item @click="editItem(item)">
                  <v-list-item-title style="cursor: pointer">
                    <v-icon color="secondary" small> mdi-pencil </v-icon>
                    Edit
                  </v-list-item-title>
                </v-list-item>
                <v-list-item @click="deleteItem(item)">
                  <v-list-item-title style="cursor: pointer">
                    <v-icon color="error" small> mdi-delete </v-icon>
                    Delete
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </template>
        </v-data-table>
      </v-card>
    </div>

    <Preloader v-else />
  </div>
  <NoAccess v-else />
</template>

<script>
import Back from "../../components/Snippets/Back.vue";

export default {
  components: { Back },

  data: () => ({
    datatable_search_textbox: "",
    datatable_searchById: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    color: "primary",
    e1: 1,
    menu2: false,
    dialog: false,
    editDialog: false,
    preloader: false,
    loading: false,
    response: false,
    id: "",
    snackbar: false,
    data: [],
    options: {},
    errors: [],
    isEdit: false,
    editedItemId: "",
    Model: "ModelRoster",
    endpoint: "roster",
    search: "",
    snackbar: false,
    ids: [],
    loading: false,
    total: 0,
    headers: [
      { text: "Day" },
      { text: "Shift Type" },
      { text: "From" },
      { text: "To" },
    ],
    days: [],
    date: [],
    data: [],
    shifts: [],
    editShifts: [],
    editName: "",
    shift: [],
    editJson: [],
    shift_ids: [],
    shiftNames: [],
    times: [],
    scheduleData: [],
    name: "",
    errors: [],
    selectedDays: [],
    edit_arr: [],
    headers_table: [
      {
        text: "#",
        align: "left",
        sortable: true,

        value: "sno", // template name
      },
      {
        text: "Name",
        align: "left",
        sortable: true,
        key: "name",
        value: "name",
      },
      {
        text: "Description",
        align: "left",
        sortable: true,
        key: "name",
        value: "description",
      },
      {
        text: "Options",
        align: "left",
        sortable: false,
        key: "name",
        value: "options",
      },
    ],
  }),

  watch: {
    dialog() {
      this.shift = {};
      this.errors = [];
      this.isEdit ? "" : (this.name = "");
    },
  },
  computed: {
    formTitle() {
      return this.isEdit ? "Edit" : "New";
    },
  },

  created() {
    this.preloader = false;
    this.id = this.$auth?.user?.company?.id;
    this.get_days();
    this.get_shifts();
    this.get_schedule();
  },
  methods: {
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
      //this.datatable_search_textbox = '';
    },
    getUpdateData(index, shift_id, day) {},

    can(per) {
      return this.$pagePermission.can(per, this);
    },
    

    editItem(item) {
      this.editJson = item.json;
      this.editShifts = item.json;
      this.editName = item.name;
      this.isEdit = true;
      this.errors = [];
      this.editedItemId = item.id;
      // this.dialog = true;
      this.editDialog = true;
    },

    close() {
      this.editDialog = false;
      this.dialog = false;
      this.isEdit = false;
    },

    update_schedule() {
      let payload = {
        json: this.editShifts,
        company_id: this.$auth?.user?.company?.id,
        name: this.editName,
      };
      // return;
      this.$axios
        .put("/roster" + "/" + this.editedItemId, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.get_schedule();
            this.isEdit = false;
            this.editDialog = false;
            this.snackbar = data.status;
            this.response = data.message;
          }
        })
        .catch((err) => console.log(err));
    },

    getShiftType(id) {
      if (id == -1) {
        return "(H)";
      } else if (id == -2) {
        return "(A)";
      } else if (id == 33) {
        return "(FL)";
      }
    },

    save() {
      this.shiftNames = [];
      for (let x in this.shift) {
        let filteredData = this.shifts.find((e) => e.id == this.shift[x]);
        let name = filteredData.name || "---";
        this.shiftNames.push(name);
      }
      let payload = {
        days: this.days,
        shift_ids: this.shift,
        shift_names: this.shiftNames,
        company_id: this.$auth?.user?.company?.id,
        name: this.name,
      };

      if (this.isEdit) {
        this.$axios
          .put("/roster" + "/" + this.editedItemId, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.get_schedule();
              this.isEdit = false;
              this.dialog = false;
              this.snackbar = data.status;
              this.response = data.message;
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post("/roster", payload)
          .then(({ data }) => {
            this.loading = false;
            if (!data.status) {
              this.errors = data.errors;
              return;
            }
            this.get_schedule();
            this.isEdit = false;
            this.dialog = false;
            this.snackbar = data.status;
            this.response = data.message;
          })
          .catch((e) => console.log(e));
      }
    },

    getTimeRange(id) {
      let { on_duty_time: on, off_duty_time: off } = this.shifts.find(
        (e) => e.id == id
      );
      let range = id == -1 || id == -2 ? "---" : on + "-" + off;
      if (!this.times.includes(range)) {
        this.times.push(range);
      }
      return;
    },

    getFrom(id) {
      let shift = this.shifts.find((e) => e.id == id);
      return (shift && shift.on_duty_time) || "---";
    },

    getTo(id) {
      let shift = this.shifts.find((e) => e.id == id);
      return (shift && shift.off_duty_time) || "---";
    },

    get_days() {
      const today = new Date();
      const currentDay = today.getDay(); // Get the current day (0 - Sunday, 1 - Monday, ..., 6 - Saturday)
      const firstDayOfWeek = new Date(today);

      // Calculate the difference to the first day of the week (Monday)
      const diff = today.getDate() - currentDay + (currentDay === 0 ? -6 : 1);
      firstDayOfWeek.setDate(diff);

      const days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]; // Adjusted the order to start from Monday.

      for (let i = 0; i < 7; i++) {
        const currentDate = new Date(firstDayOfWeek);
        currentDate.setDate(firstDayOfWeek.getDate() + i);
        this.days.push(days[currentDate.getDay()]);
        this.date.push(currentDate.toISOString().split("T")[0]);
      }
    },

    get_shifts() {
      let options = {
        per_page: 1000,
        company_id: this.$auth.user.company_id,
      };
      this.$axios.get("shift", { params: options }).then(({ data }) => {
        let arr = [
          {
            id: -1,
            name: "Off",
          },
          {
            id: -2,
            name: "AutoShift",
          },
        ];
        this.shifts = data.data.concat(arr);
      });
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete("roster" + "/" + item.id)
          .then(({ data }) => {
            this.snackbar = data.status;
            this.get_schedule();
            this.response = data.message;
          })
          .catch((err) => console.log(err));
    },

    get_schedule(filter_column = "", filter_value = "") {
      this.loading = true;
      let options = {
        per_page: 20,
        company_id: this.$auth.user.company_id,
      };

      if (filter_value != "") options[filter_column] = filter_value;
      this.$axios.get("roster", { params: options }).then(({ data }) => {
        if (filter_column != "" && data.data.length == 0) {
          this.snack = true;
          this.snackColor = "error";
          this.snackText = "No Results Found";
          this.loading = false;
          return false;
        }
        this.scheduleData = data.data;
        this.loading = false;
      });
    },
  },
};
</script>
<style scoped>
td,
th {
  border: 1px solid #dddddd;
  padding-left: 5px;
}

* {
  box-sizing: border-box;
}

table {
  width: 100%;
  font-size: 13px;
  color: #444;
  white-space: nowrap;
  border-collapse: collapse;
}

table > thead {
  background-color: #00bcd4;
  color: #fff;
}

table > thead th {
  padding: 15px;
}

table th,
table td {
  border: 1px solid #00000017;
  padding: 10px 15px;
}

table > tbody > tr > td > img {
  display: inline-block;
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 50%;
  border: 4px solid #fff;
  box-shadow: 0 2px 6px #0003;
}

table > tbody > tr {
  background-color: #fff;
  transition: 0.3s ease-in-out;
}

table > tbody > tr:nth-child(even) {
  background-color: rgb(238, 238, 238);
}

table > tbody > tr:hover {
  filter: drop-shadow(0px 2px 6px #0002);
}
</style>
