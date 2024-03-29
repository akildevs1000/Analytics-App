<template>
  <div v-if="can('setting_company_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <div v-if="!preloader">
      <!-- <v-row class="mt-5 mb-5">
        <v-col cols="10">
          <h3>Report Notification</h3>
          <div>Dashboard / Report Notification</div>
        </v-col>
      </v-row> -->
      <!-- <Back class="primary white--text" /> -->

      <v-card elevation="0" class="mt-2 pa-3">
        <v-card-title style="border-bottom: 1px solid #ddd">
          <span class="popup_title"> Create Report Notification </span>
        </v-card-title>
        <v-container>
          <v-row>
            <v-col v-if="isCompany" cols="6">
              <v-select
                v-model="payload.branch_id"
                :items="branchesList"
                dense
                placeholder="Select Branch"
                outlined
                item-value="id"
                item-text="branch_name"
              >
              </v-select>
            </v-col>
            <v-col cols="6">
              <v-text-field
                :hide-details="!payload.subject"
                v-model="payload.subject"
                placeholder="Title/Subject"
                outlined
                dense
              ></v-text-field>

              <span v-if="errors && errors.subject" class="error--text">{{
                errors.subject[0]
              }}</span>

              <span v-if="errors && errors.subject" class="error--text">{{
                errors.subject[0]
              }}</span>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="4">
              <v-autocomplete
                @change="setDay"
                :hide-details="!payload.frequency"
                v-model="payload.frequency"
                outlined
                dense
                placeholder="Frequency"
                :items="['Daily', 'Weekly', 'Monthly']"
              >
              </v-autocomplete>
              <span v-if="errors && errors.frequency" class="error--text">{{
                errors.frequency[0]
              }}</span>
            </v-col>
            <v-col cols="4">
              <v-autocomplete
                v-if="
                  payload.frequency == 'Daily' || payload.frequency == 'Weekly'
                "
                :hide-details="!payload.day"
                v-model="payload.day"
                outlined
                dense
                placeholder="Days"
                :items="payload.frequency == 'Weekly' ? days : []"
                item-text="name"
                item-value="id"
              >
              </v-autocomplete>
              <v-menu
                v-if="payload.frequency == 'Monthly'"
                class="mt-2"
                ref="menu"
                v-model="menu"
                :close-on-content-click="false"
                :return-value.sync="payload.date"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    :hide-details="payload.date"
                    outlined
                    dense
                    v-model="payload.date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="payload.date" no-title scrollable>
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    text
                    color="primary"
                    @click="set_date_save($refs.menu, payload.date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
              <span v-if="errors && errors.day" class="error--text">{{
                errors.day[0]
              }}</span>
              <span v-if="errors && errors.date" class="error--text">{{
                errors.date[0]
              }}</span>
            </v-col>

            <v-col cols="4">
              <v-menu
                ref="menu"
                v-model="menu2"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="payload.time"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    :hide-details="!payload.time"
                    outlined
                    dense
                    v-model="payload.time"
                    placeholder="Time"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-time-picker
                  v-if="menu2"
                  v-model="payload.time"
                  full-width
                  @click:minute="$refs.menu.save(payload.time)"
                ></v-time-picker>
              </v-menu>
              <span v-if="errors && errors.time" class="text-danger mt-2">{{
                errors.time[0]
              }}</span>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row dense>
            <label class="col-form-label pt-5"><b>Reports</b></label>
            <v-col cols="2" class="pa-0 ma-0">
              <v-checkbox
                dense
                v-model="payload.reports"
                label="Daily Summary"
                value="daily_summary.pdf"
              ></v-checkbox>
              <v-checkbox
                dense
                v-model="payload.reports"
                label="Daily Present"
                value="daily_present.pdf"
              ></v-checkbox>
              <v-checkbox
                dense
                v-model="payload.reports"
                label="Daily Absent"
                value="daily_absent.pdf"
              ></v-checkbox>
              <v-checkbox
                dense
                v-model="payload.reports"
                label="Daily Missing"
                value="daily_missing.pdf"
              ></v-checkbox>
              <v-checkbox
                dense
                v-model="payload.reports"
                label="Daily Manual Entry"
                value="daily_manual.pdf"
              ></v-checkbox>
              <span v-if="errors && errors.reports" class="error--text">{{
                errors.reports[0]
              }}</span>
            </v-col>
            <v-col cols="2" class="pa-0 ma-0">
              <v-checkbox
                dense
                v-model="payload.reports"
                label="Weekly Summary"
                value="weekly_summary.pdf"
              ></v-checkbox>
              <v-checkbox
                dense
                v-model="payload.reports"
                label="Weekly Present"
                value="weekly_present.pdf"
              ></v-checkbox>
              <v-checkbox
                dense
                v-model="payload.reports"
                label="Weekly Absent"
                value="weekly_absent.pdf"
              ></v-checkbox>
              <v-checkbox
                dense
                v-model="payload.reports"
                label="Weekly Missing"
                value="weekly_missing.pdf"
              ></v-checkbox>

              <v-checkbox
                dense
                v-model="payload.reports"
                label="Weekly Manual Entry"
                value="weekly_manual.pdf"
              ></v-checkbox>
            </v-col>
            <v-col cols="2" class="pa-0 ma-0">
              <v-checkbox
                dense
                v-model="payload.reports"
                label="Monthly Summary"
                value="monthly_summary.pdf"
              ></v-checkbox>
              <v-checkbox
                dense
                v-model="payload.reports"
                label="Monthly Present"
                value="monthly_present.pdf"
              ></v-checkbox>
              <v-checkbox
                dense
                v-model="payload.reports"
                label="Monthly Absent"
                value="monthly_absent.pdf"
              ></v-checkbox>
              <v-checkbox
                dense
                v-model="payload.reports"
                label="Monthly Missing"
                value="monthly_missing.pdf"
              ></v-checkbox>
              <v-checkbox
                dense
                v-model="payload.reports"
                label="Monthly Manual Entry"
                value="monthly_manual.pdf"
              ></v-checkbox>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row dense>
            <label class="col-form-label pt-5"><b>Medium </b></label>

            <v-col cols="1" class="pa-0 ma-0">
              <v-checkbox
                dense
                v-model="payload.mediums"
                label="Email"
                value="Email"
              ></v-checkbox>
            </v-col>
            <v-col cols="2" class="pa-0 mr-7">
              <v-checkbox
                dense
                v-model="payload.mediums"
                label="Whatsapp"
                value="Whatsapp"
              ></v-checkbox>
            </v-col>
            <v-col cols="12" class="pa-0 ma-0">
              <span v-if="errors && errors.mediums" class="error--text">{{
                errors.mediums[0]
              }}</span>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row>
            <v-col cols="12">
              <label class="col-form-label"> <h4>Mail Settings</h4> </label
              ><br />
            </v-col>
          </v-row>
          <v-row style="margin-top: -30px">
            <v-col cols="3">
              <label class="col-form-label"><b>Subject </b></label>

              <v-text-field
                :hide-details="!payload.subject"
                v-model="payload.subject"
                placeholder="Subject"
                outlined
                dense
              ></v-text-field>

              <span v-if="errors && errors.subject" class="error--text">{{
                errors.subject[0]
              }}</span>

              <span v-if="errors && errors.subject" class="error--text">{{
                errors.subject[0]
              }}</span>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="3">
              <label class="col-form-label pt-5"
                ><b>To </b>(Press enter to add email address/es)</label
              >

              <v-text-field
                :hide-details="!to"
                @keyup.enter="add_to"
                v-model="to"
                placeholder="Email"
                outlined
                dense
              ></v-text-field>

              <v-chip
                color="primary"
                class="ma-1"
                v-for="(item, index) in payload.tos"
                :key="index"
              >
                <span class="mx-1">{{ item }}</span>
                <v-icon small @click="deleteTO(index)"
                  >mdi-close-circle-outline</v-icon
                >
              </v-chip>
              <span v-if="errors && errors.tos" class="error--text">{{
                errors.tos[0]
              }}</span>
            </v-col>
            <v-col cols="3">
              <label class="col-form-label pt-5"
                ><b>Cc </b>(Press enter to add email address/es)</label
              >
              <v-text-field
                @keyup.enter="add_cc"
                v-model="cc"
                placeholder="Email"
                outlined
                dense
              ></v-text-field>

              <v-chip
                color="primary"
                class="ma-1"
                v-for="(item, index) in payload.ccs"
                :key="index"
              >
                <span class="mx-1">{{ item }}</span>
                <v-icon small @click="deleteCC(index)"
                  >mdi-close-circle-outline</v-icon
                >
              </v-chip>
            </v-col>
            <v-col cols="3">
              <label class="col-form-label pt-5"
                ><b>Bcc </b>(Press enter to add email address/es)</label
              >
              <v-text-field
                @keyup.enter="add_bcc"
                v-model="bcc"
                placeholder="Email"
                outlined
                dense
              ></v-text-field>

              <v-chip
                color="primary"
                class="ma-1"
                v-for="(item, index) in payload.bccs"
                :key="index"
              >
                <span class="mx-1">{{ item }}</span>
                <v-icon small @click="deleteBCC(index)"
                  >mdi-close-circle-outline</v-icon
                >
              </v-chip>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12">
              <ClientOnly>
                <tiptap-vuetify
                  class="tiptap-icon"
                  v-model="payload.body"
                  :extensions="extensions"
                  v-scroll.self="onScroll"
                  max-height="400"
                  :toolbar-attributes="{
                    color: 'background red--text',
                  }"
                />
                <template #placeholder> Loading... </template>
              </ClientOnly>
            </v-col>
            <v-spacer></v-spacer>
            <v-col col="2" class="text-end">
              <v-btn small color="primary" @click="store"> Submit </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </div>
    <Preloader v-else />
  </div>
  <NoAccess v-else />
</template>

<script>
import {
  TiptapVuetify,
  Heading,
  Bold,
  Italic,
  Strike,
  Underline,
  Paragraph,
  BulletList,
  OrderedList,
  ListItem,
  Blockquote,
  History,
} from "tiptap-vuetify";

export default {
  components: { TiptapVuetify },

  data: () => ({
    menu: false,
    days: [
      { id: 1, name: "Monday" },
      { id: 2, name: "Tuesday" },
      { id: 3, name: "Wednesday" },
      { id: 4, name: "Thursday" },
      { id: 5, name: "Friday" },
      { id: 6, name: "Saturday" },
      { id: 0, name: "Sunday" },
    ],
    extensions: [
      History,
      Blockquote,
      Underline,
      Strike,
      Italic,
      ListItem,
      BulletList,
      OrderedList,
      [
        Heading,
        {
          options: {
            levels: [1, 2, 3],
          },
        },
      ],
      Bold,
      Paragraph,
    ],
    // starting editor's content
    content: `
      <h1>Yay Headlines!</h1>
      <p>All these <strong>cool tags</strong> are working now.</p>
        `,
    color: "primary",
    e1: 1,
    menu2: false,
    preloader: false,
    loading: false,
    response: false,
    id: "",
    snackbar: false,
    to: "",

    number: "",
    cc: "",
    bcc: "",
    payload: {
      day: 1,
      reports: [],
      mediums: [],
      frequency: "Daily",
      time: null,
      tos: [],
      ccs: [],
      bccs: [],
      date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10),
      company_id: 0,
    },

    errors: [],
    branchesList: [],
    branch_id: "",
    isCompany: true,
  }),

  async created() {
    this.preloader = false;
    this.payload.company_id = this.$auth?.user?.company?.id;

    if (this.$auth.user.branch_id) {
      this.payload.branch_id = this.$auth.user.branch_id;
      this.isCompany = false;
      return;
    }

    try {
      const { data } = await this.$axios.get(`branches_list`, {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company_id,
        },
      });
      this.branchesList = data;
    } catch (error) {
      // Handle the error
      console.error("Error fetching branch list", error);
    }
  },
  methods: {
    setDay() {
      let { frequency, day, date } = this.payload;

      if (frequency == "Monthly") {
        day = new Date(date).getDate();
      }

      this.payload.day = day;
    },
    onScroll() {
      this.scrollInvoked++;
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    

    add_number() {
      if (this.number && this.number.length > 10) {
        this.payload.numbers.push(this.number);
        this.number = "";
      }
    },
    add_to() {
      this.payload.tos.push(this.to);
      this.to = "";
    },
    add_cc() {
      this.payload.ccs.push(this.cc);
      this.cc = "";
    },
    add_bcc() {
      this.payload.bccs.push(this.bcc);
      this.bcc = "";
    },
    deleteTO(i) {
      this.payload.tos.splice(i, 1);
    },

    deleteNumber(i) {
      this.payload.numbers.splice(i, 1);
    },

    deleteCC(i) {
      this.payload.ccs.splice(i, 1);
    },

    deleteBCC(i) {
      this.payload.bccs.splice(i, 1);
    },

    store() {
      this.$axios
        .post("/report_notification", this.payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
            return;
          }

          this.snackbar = data.status;
          this.response = data.message;

          setTimeout(() => {
            this.$router.push("/report_notifications");
          }, 1000);
        })
        .catch((e) => console.log(e));
    },
    test_endpoint() {
      // /test/whatsapp
      this.$axios.get("/test/whatsapp").then((res) => {});
    },
    test() {
      var axios = require("axios");
      // var data = JSON.stringify({
      //   messaging_product: "whatsapp",
      //   recipient_type: "individual",
      //   to: "923108559858",
      //   type: "text",
      //   text: {
      //     preview_url: false,
      //     body: "contect"
      //   }
      //   // type: "text",
      //   // text: {
      //   //   // the text object
      //   //   preview_url: false,
      //   //   body: "sdfsdf"
      //   // }
      // });

      var data = JSON.stringify({
        messaging_product: "whatsapp",
        to: "923108559858",
        type: "template",
        template: {
          name: "automated_reports",
          language: {
            code: "en",
          },
        },
      });

      var config = {
        method: "post",
        url: "https://graph.facebook.com/v14.0/102482416002121/messages",
        headers: {
          "Content-Type": "application/json",
          Authorization:
            "Bearer EAAP9IfKKSo0BAGDS96w2XuYjjpXIqxZBAOcwzlFWecCxODjNO3ruEcbnZCkmHSWNAGNf1Q9wC2uwe5XnyxteTOYAO3l9wgy4iu9L6wwYgtZBZAygXV3Tc4euoYANOZCFlvMAsnNz7vNQEYUYdL56l9poliM3eS6ZCZBV4dMzJhKEQKDbUTZB2ZBvEVl2mlHvSj8dCWgITF8e9GFkTXO8isMsx",
        },
        data: data,
      };

      axios(config)
        .then(function (response) {})
        .catch(function (error) {
          console.log(error);
        });
    },
  },
};
</script>
<style>
.tiptap-vuetify-editor__content {
  min-height: 400px !important;
}

.ProseMirror .ProseMirror-focused {
  height: 400px !important;
}

.tiptap-icon .v-icon {
  color: white !important;
}

.tiptap-icon .v-btn--icon {
  color: white !important;
}
</style>
