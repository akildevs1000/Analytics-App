<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row>
      <v-col cols="12" md="12">
        Shift Name <span class="error--text">*</span>
        <v-text-field
          :hide-details="!errors.name"
          :error-messages="errors.name && errors.name[0]"
          class="mt-1"
          outlined
          dense
          v-model="payload.name"
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="3">
        On Duty Time <span class="error--text">*</span>
        <v-menu
          ref="time_in_menu_ref"
          v-model="time_in_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="payload.on_duty_time"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-height="320px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              append-icon="mdi-clock-outline"
              :hide-details="!errors.on_duty_time"
              v-model="payload.on_duty_time"
              readonly
              v-bind="attrs"
              v-on="on"
              dense
              outlined
              class="mt-2"
              :class="payload.shift_type_id !== 1 ? '' : 'red lighten-1'"
            ></v-text-field>
          </template>
          <v-time-picker
            v-if="time_in_menu"
            v-model="payload.on_duty_time"
            full-width
            format="24hr"
          >
            <v-spacer></v-spacer>
            <v-btn x-small color="primary" @click="time_in_menu = false">
              Cancel
            </v-btn>
            <v-btn
              x-small
              color="primary"
              @click="$refs.time_in_menu_ref.save(payload.on_duty_time)"
            >
              OK
            </v-btn>
          </v-time-picker>
        </v-menu>
        <span v-if="errors && errors.on_duty_time" class="text-danger mt-2">{{
          errors.on_duty_time[0]
        }}</span>
      </v-col>
      <v-col cols="12" md="3">
        Off Duty Time <span class="error--text">*</span>
        <v-menu
          ref="time_out_menu_ref"
          v-model="time_out_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="payload.off_duty_time"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-height="320px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              append-icon="mdi-clock-outline"
              v-model="payload.off_duty_time"
              readonly
              :hide-details="!errors.off_duty_time"
              v-bind="attrs"
              v-on="on"
              dense
              outlined
              class="mt-2"
            ></v-text-field>
          </template>
          <v-time-picker
            format="24hr"
            v-if="time_out_menu"
            v-model="payload.off_duty_time"
            full-width
          >
            <v-spacer></v-spacer>
            <v-btn x-small color="primary" @click="time_out_menu = false">
              Cancel
            </v-btn>
            <v-btn
              x-small
              color="primary"
              @click="$refs.time_out_menu_ref.save(payload.off_duty_time)"
            >
              OK
            </v-btn>
          </v-time-picker>
        </v-menu>
        <span v-if="errors && errors.off_duty_time" class="text-danger mt-2">{{
          errors.off_duty_time[0]
        }}</span>
      </v-col>

      <v-col cols="12" md="3">
        Gap Time For Checkin <span class="error--text">*</span>
        <div class="wrapper">
          <span class="minus" @click="payload.gap_in < 1 || payload.gap_in--"
            >-</span
          >
          <span class="num">{{ payload.gap_in }}</span>
          <span class="plus" @click="payload.gap_in >= 59 || payload.gap_in++"
            >+</span
          >
        </div>
      </v-col>

      <v-col cols="12" md="3">
        Gap Time For Checkout <span class="error--text">*</span>
        <div class="wrapper">
          <span class="minus" @click="payload.gap_out < 1 || payload.gap_out--"
            >-</span
          >
          <span class="num">{{ payload.gap_out }}</span>
          <span class="plus" @click="payload.gap_out >= 59 || payload.gap_out++"
            >+</span
          >
        </div>
      </v-col>
      <v-col cols="12" md="3">
        Minimum Working Hours <span class="error--text">*</span>
        <v-menu
          ref="working_hours_menu_ref"
          v-model="working_hours_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="payload.working_hours"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              append-icon="mdi-clock-outline"
              v-model="payload.working_hours"
              readonly
              :hide-details="!errors.working_hours"
              v-bind="attrs"
              v-on="on"
              dense
              outlined
              class="mt-2"
            ></v-text-field>
          </template>
          <v-time-picker
            format="24hr"
            v-if="working_hours_menu"
            v-model="payload.working_hours"
            full-width
          >
            <v-spacer></v-spacer>
            <v-btn x-small color="primary" @click="working_hours_menu = false">
              Cancel
            </v-btn>
            <v-btn
              x-small
              color="primary"
              @click="$refs.working_hours_menu_ref.save(payload.working_hours)"
            >
              OK
            </v-btn>
          </v-time-picker>
        </v-menu>
        <span v-if="errors && errors.working_hours" class="text-danger mt-2">{{
          errors.working_hours[0]
        }}</span>
      </v-col>

      <v-col cols="12" md="3">
        Overtime start after duty hours <span class="error--text">*</span>

        <v-menu
          ref="overtime_interval_menu_ref"
          v-model="overtime_interval_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="payload.overtime_interval"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              append-icon="mdi-clock-outline"
              v-model="payload.overtime_interval"
              readonly
              :hide-details="!errors.working_hours"
              v-bind="attrs"
              v-on="on"
              dense
              outlined
              class="mt-2"
            ></v-text-field>
          </template>
          <v-time-picker
            format="24hr"
            v-if="overtime_interval_menu"
            v-model="payload.overtime_interval"
            full-width
          >
            <v-spacer></v-spacer>
            <v-btn
              x-small
              color="primary"
              @click="overtime_interval_menu = false"
            >
              Cancel
            </v-btn>
            <v-btn
              x-small
              color="primary"
              @click="
                $refs.overtime_interval_menu_ref.save(payload.overtime_interval)
              "
            >
              OK
            </v-btn>
          </v-time-picker>
        </v-menu>
        <span
          v-if="errors && errors.overtime_interval"
          class="text-danger mt-2"
          >{{ errors.overtime_interval[0] }}</span
        >
      </v-col>

      <!-- <v-col cols="12" md="12">
        <b>Holidays</b>
        <br />
        <v-checkbox
          style="float: left"
          class="mr-5"
          v-for="(week_day, index) in week_days"
          :key="index"
          v-model="payload.days"
          :label="week_day.label"
          :value="week_day.value"
          :error-messages="errors.days && errors.days[0]"
        ></v-checkbox>
      </v-col> -->
    </v-row>
    <v-row>
      <v-col cols="12">
        <div class="text-left">
          <v-btn
            v-if="can(`shift_create`)"
            small
            color="primary"
            @click="store_shift"
          >
            Submit
          </v-btn>
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  props: ["shift_type_id", "shift_id"],
  data: () => ({
    manual_shift: {},

    date: null,
    menu: false,

    time_tables: [],
    shift_types: [],
    shift_last_id: "",
    shiftList: true,
    isChange: false,
    isAuto: false,

    week_days: [
      { label: "Sun", value: "Sun" },
      { label: "Mon", value: "Mon" },
      { label: "Tue", value: "Tue" },
      { label: "Wed", value: "Wed" },
      { label: "Thu", value: "Thu" },
      { label: "Fri", value: "Fri" },
      { label: "Sat", value: "Sat" },
    ],

    loading: false,
    time_in_menu: false,
    time_out_menu: false,

    gap_in_menu: false,
    gap_out_menu: false,

    working_hours_menu: false,
    overtime_interval_menu: false,

    grace_time_in_menu: false,
    grace_time_out_menu: false,

    beginning_in_menu: false,
    ending_in_menu: false,

    beginning_out_menu: false,
    ending_out_menu: false,

    payload: {
      name: "Multi Shift",
      on_duty_time: "17:00",
      off_duty_time: "14:00",
      gap_in: 15,
      gap_out: 15,
      working_hours: "09:00",
      overtime_interval: "00:00",
      // days: []
    },

    errors: [],
    shifts: [],
    data: [],
    response: "",
    snackbar: false,
  }),
  async created() {
    this.$axios
      .get(`/shift/${this.shift_id}`)
      .then(({ data }) => {
        this.loading = false;
        this.payload = {
          name: data.name,
          on_duty_time: data.on_duty_time,
          off_duty_time: data.off_duty_time,
          gap_in: data.gap_in,
          gap_out: data.gap_out,
          working_hours: data.working_hours,
          overtime_interval: data.overtime_interval,
          company_id: this.$auth.user.company_id,
          shift_type_id: data.shift_type_id,
        };
      })
      .catch(({ message }) => {
        this.snackbar = true;
        this.response = message;
      });
  },
  watch: {},
  computed: {},
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    

    store_shift() {
      this.loading = true;

      this.$axios
        .put(`/shift/${this.shift_id}`, this.payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.snackbar = true;
            this.response = "Shift update successfully";
            setTimeout(() => {
              this.$router.push("/shift");
            }, 1000);
          }
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    },
  },
};
</script>

<style scoped>
.wrapper {
  height: 47px;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border-radius: 5px;
  border: 1px solid rgb(145, 140, 140);
}

.wrapper span {
  width: 100%;
  text-align: center;
  font-size: 25px;
  font-weight: normal;
  cursor: pointer;
  user-select: none;
}

.wrapper span.num {
  font-size: 20px;
  border-right: 2px solid rgba(0, 0, 0, 0.2);
  border-left: 2px solid rgba(0, 0, 0, 0.2);
  pointer-events: none;
}
</style>
