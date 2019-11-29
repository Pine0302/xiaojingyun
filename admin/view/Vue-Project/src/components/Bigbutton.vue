<template>
    <div class="WSY_text_input">
        <input  type="button" v-model="btnval" class="WSY_button" :class="skin">
    </div>
</template>

<script>
export default {
  name: "Bigbutton",
  props: ["btnval"],
  data() {
    return {
      skin: ""
    };
  },
  created: function() {
    var self = this;
    //换肤
    self.$http
      .post("mshop/admin/index.php?m=setting&a=get_shop_skin", {})
      .then(res => {
        self.skin = res.data.skin;
      })
      .catch(err => {
        console.log(err);
      });
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >
.WSY_button {
  margin-top: 30px;
  width: 120px;
  height: 40px;
  background-color: #06a7e1;
  border: none;
  font-size: 16px;
  color: #f9fdff;
  border-radius: 2px;
  border: solid 1px #06a7e1;
  text-align: center;
}
</style>
