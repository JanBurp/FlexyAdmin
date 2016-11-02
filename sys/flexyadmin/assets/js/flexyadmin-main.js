/**
 * Bootstrapping FlexyAdmin:
 * - Create Vue Instance
 * 
 * @author: Jan den Besten
 */



// Every component logs its name and props
// Vue.mixin({
//   created: function () {
//     if (this.$options._componentTag) console.log(this.$options._componentTag, this.$options.propsData);
//   },
// });

new Vue({
  el:'#main',
});
