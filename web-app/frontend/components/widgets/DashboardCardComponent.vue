<template>
  <v-row>
    <v-col
      v-for="(card, index) in cardData"
      :key="index"
      :cols="card.cols"
      :sm="card.sm"
      :md="card.md"
    >
      <v-card
        :color="card.color"
        dark
        dense
        style="border-radius: 15px !important"
      >
        <!-- <div class="text-right px-2">
          <v-icon small @click="goToThemeEditor">mdi-pencil</v-icon>
        </div> -->
        <div class="text-center pt-5 pb-1">
          <h5>{{ card.calculated_value }}</h5>
          <p>{{ card.title }}</p>
        </div>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: ["page"],
  data() {
    return {
      editDialog: false,
      cardData: [],
    };
  },
  mounted() {
    this.getRecord();
  },
  methods: {
    getRecord() {
      let payload = {
        page: this.page,
        type: "card",
        //department_ids: this.$auth.user.assignedDepartments,
        company_id: this.$auth.user.company_id,
      };

      this.$axios
        .get("theme", { params: payload })
        .then(({ data }) => {
          this.cardData = data;
        })
        .catch((e) => console.log(e));
    },

    goToThemeEditor() {
      this.$router.push(`theme/${this.page}`);
    },
  },
};
</script>
