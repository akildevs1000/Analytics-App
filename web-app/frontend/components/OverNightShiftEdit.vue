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
        <v-menu
          ref="time_in_menu_ref"
          v-model="time_in_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="payload.on_duty_time"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            On Duty Time
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
        <v-menu
          ref="beginning_in_menu_ref"
          v-model="beginning_in_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="payload.beginning_in"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            Beginning In
            <v-text-field
              append-icon="mdi-clock-outline"
              v-model="payload.beginning_in"
              readonly
              :hide-details="!errors.beginning_in"
              v-bind="attrs"
              v-on="on"
              dense
              outlined
              class="mt-2"
            ></v-text-field>
          </template>
          <v-time-picker
            v-if="beginning_in_menu"
            v-model="payload.beginning_in"
            full-width
            format="24hr"
          >
            <v-spacer></v-spacer>
            <v-btn x-small color="primary" @click="beginning_in_menu = false">
              Cancel
            </v-btn>
            <v-btn
              x-small
              color="primary"
              @click="$refs.beginning_in_menu_ref.save(payload.beginning_in)"
            >
              OK
            </v-btn>
          </v-time-picker>
        </v-menu>
        <span v-if="errors && errors.beginning_in" class="text-danger mt-2">{{
          errors.beginning_in[0]
        }}</span>
      </v-col>
      <v-col cols="12" md="3">
        <v-menu
          ref="ending_in_menu_ref"
          v-model="ending_in_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="payload.ending_in"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            Ending In
            <v-text-field
              append-icon="mdi-clock-outline"
              v-model="payload.ending_in"
              readonly
              :hide-details="!errors.ending_in"
              v-bind="attrs"
              v-on="on"
              dense
              outlined
              class="mt-2"
            ></v-text-field>
          </template>
          <v-time-picker
            v-if="ending_in_menu"
            v-model="payload.ending_in"
            full-width
            format="24hr"
          >
            <v-spacer></v-spacer>
            <v-btn x-small color="primary" @click="ending_in_menu = false">
              Cancel
            </v-btn>
            <v-btn
              x-small
              color="primary"
              @click="$refs.ending_in_menu_ref.save(payload.ending_in)"
            >
              OK
            </v-btn>
          </v-time-picker>
        </v-menu>
        <span v-if="errors && errors.ending_in" class="text-danger mt-2">{{
          errors.ending_in[0]
        }}</span>
      </v-col>

      <v-col cols="12" md="3">
        Late Time <span class="error--text">*</span>

        <v-menu
          ref="late_time_menu_ref"
          v-model="late_time_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="payload.late_time"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              append-icon="mdi-clock-outline"
              v-model="payload.late_time"
              :hide-details="!errors.late_time"
              readonly
              v-bind="attrs"
              v-on="on"
              dense
              outlined
              class="mt-2"
            ></v-text-field>
          </template>
          <v-time-picker
            v-if="late_time_menu"
            v-model="payload.late_time"
            full-width
            format="24hr"
          >
            <v-spacer></v-spacer>
            <v-btn x-small color="primary" @click="late_time_menu = false">
              Cancel
            </v-btn>
            <v-btn
              x-small
              color="primary"
              @click="$refs.late_time_menu_ref.save(payload.late_time)"
            >
              OK
            </v-btn>
          </v-time-picker>
        </v-menu>
        <span v-if="errors && errors.late_time" class="text-danger mt-2">{{
          errors.late_time[0]
        }}</span>
      </v-col>

      <v-col cols="12" md="3">
        <v-menu
          ref="time_out_menu_ref"
          v-model="time_out_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="payload.off_duty_time"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            Off Duty Time
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
        <v-menu
          ref="beginning_out_menu_ref"
          v-model="beginning_out_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="payload.beginning_out"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            Beginning Out
            <v-text-field
              append-icon="mdi-clock-outline"
              v-model="payload.beginning_out"
              readonly
              :hide-details="!errors.beginning_out"
              v-bind="attrs"
              v-on="on"
              dense
              outlined
              class="mt-2"
            ></v-text-field>
          </template>
          <v-time-picker
            v-if="beginning_out_menu"
            v-model="payload.beginning_out"
            full-width
            format="24hr"
          >
            <v-spacer></v-spacer>
            <v-btn x-small color="primary" @click="beginning_out_menu = false">
              Cancel
            </v-btn>
            <v-btn
              x-small
              color="primary"
              @click="$refs.beginning_out_menu_ref.save(payload.beginning_out)"
            >
              OK
            </v-btn>
          </v-time-picker>
        </v-menu>
        <span v-if="errors && errors.beginning_out" class="text-danger mt-2">{{
          errors.beginning_out[0]
        }}</span>
      </v-col>
      <v-col cols="12" md="3">
        <v-menu
          ref="ending_out_menu_ref"
          v-model="ending_out_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="payload.ending_out"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            Ending Out
            <v-text-field
              append-icon="mdi-clock-outline"
              v-model="payload.ending_out"
              :hide-details="!errors.ending_out"
              readonly
              v-bind="attrs"
              v-on="on"
              dense
              outlined
              class="mt-2"
            ></v-text-field>
          </template>
          <v-time-picker
            v-if="ending_out_menu"
            v-model="payload.ending_out"
            full-width
            format="24hr"
          >
            <v-spacer></v-spacer>
            <v-btn x-small color="primary" @click="ending_out_menu = false">
              Cancel
            </v-btn>
            <v-btn
              x-small
              color="primary"
              @click="$refs.ending_out_menu_ref.save(payload.ending_out)"
            >
              OK
            </v-btn>
          </v-time-picker>
        </v-menu>
        <span v-if="errors && errors.ending_out" class="text-danger mt-2">{{
          errors.ending_out[0]
        }}</span>
      </v-col>
      <v-col cols="12" md="3">
        <v-menu
          ref="early_time_menu_ref"
          v-model="early_time_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="payload.early_time"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            Early Time
            <v-text-field
              append-icon="mdi-clock-outline"
              v-model="payload.early_time"
              :hide-details="!errors.early_time"
              readonly
              v-bind="attrs"
              v-on="on"
              dense
              outlined
              class="mt-2"
            ></v-text-field>
          </template>
          <v-time-picker
            v-if="early_time_menu"
            v-model="payload.early_time"
            full-width
            format="24hr"
          >
            <v-spacer></v-spacer>
            <v-btn x-small color="primary" @click="early_time_menu = false">
              Cancel
            </v-btn>
            <v-btn
              x-small
              color="primary"
              @click="$refs.early_time_menu_ref.save(payload.early_time)"
            >
              OK
            </v-btn>
          </v-time-picker>
        </v-menu>
        <span v-if="errors && errors.early_time" class="text-danger mt-2">{{
          errors.early_time[0]
        }}</span>
      </v-col>
      <v-col cols="12" md="3">
        <v-menu
          ref="absent_min_in_menu_ref"
          v-model="absent_min_in_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="payload.absent_min_in"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            Absent In
            <v-text-field
              append-icon="mdi-clock-outline"
              v-model="payload.absent_min_in"
              :hide-details="!errors.absent_min_in"
              readonly
              v-bind="attrs"
              v-on="on"
              dense
              outlined
              class="mt-2"
            ></v-text-field>
          </template>
          <v-time-picker
            v-if="absent_min_in_menu"
            v-model="payload.absent_min_in"
            full-width
            format="24hr"
          >
            <v-spacer></v-spacer>
            <v-btn x-small color="primary" @click="absent_min_in_menu = false">
              Cancel
            </v-btn>
            <v-btn
              x-small
              color="primary"
              @click="$refs.absent_min_in_menu_ref.save(payload.absent_min_in)"
            >
              OK
            </v-btn>
          </v-time-picker>
        </v-menu>
        <span v-if="errors && errors.absent_min_in" class="text-danger mt-2">{{
          errors.absent_min_in[0]
        }}</span>
      </v-col>
      <v-col cols="12" md="3">
        <v-menu
          ref="absent_min_out_menu_ref"
          v-model="absent_min_out_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="payload.absent_min_out"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            Absent Out
            <v-text-field
              append-icon="mdi-clock-outline"
              v-model="payload.absent_min_out"
              :hide-details="!errors.absent_min_out"
              readonly
              v-bind="attrs"
              v-on="on"
              dense
              outlined
              class="mt-2"
            ></v-text-field>
          </template>
          <v-time-picker
            v-if="absent_min_out_menu"
            v-model="payload.absent_min_out"
            full-width
            format="24hr"
          >
            <v-spacer></v-spacer>
            <v-btn x-small color="primary" @click="absent_min_out_menu = false">
              Cancel
            </v-btn>
            <v-btn
              x-small
              color="primary"
              @click="
                $refs.absent_min_out_menu_ref.save(payload.absent_min_out)
              "
            >
              OK
            </v-btn>
          </v-time-picker>
        </v-menu>
        <span v-if="errors && errors.absent_min_out" class="text-danger mt-2">{{
          errors.absent_min_out[0]
        }}</span>
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
            @click="submit"
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
    date: null,
    menu: false,

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
    working_hours_menu: false,
    overtime_interval_menu: false,
    time_in_menu: false,
    time_out_menu: false,
    grace_time_in_menu: false,
    grace_time_out_menu: false,

    late_time_menu: false,
    early_time_menu: false,

    beginning_in_menu: false,
    ending_in_menu: false,

    absent_min_in_menu: false,
    absent_min_out_menu: false,

    beginning_out_menu: false,
    ending_out_menu: false,

    payload: {
      on_duty_time: "21:00",
      beginning_in: "20:00",
      ending_in: "01:00",
      late_time: "00:15",
      off_duty_time: "06:00",
      beginning_out: "05:00",
      ending_out: "07:00",
      early_time: "00:15",
      absent_min_in: "01:00",
      absent_min_out: "01:00",
      working_hours: "09:00",
      overtime_interval: "00:30",
      days: ["Sun"],
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
          beginning_in: data.beginning_in,
          ending_in: data.ending_in,
          late_time: data.late_time,
          off_duty_time: data.off_duty_time,
          beginning_out: data.beginning_out,
          ending_out: data.ending_out,
          early_time: data.early_time,
          absent_min_in: data.absent_min_in,
          absent_min_out: data.absent_min_out,
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
    
    submit() {
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
