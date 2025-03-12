import { defineStore } from "pinia";

export const useUserStore = defineStore("user", () => {
  const isAuth = ref(false);
  const name = ref("");
  const phone = ref("");

  return { name, phone, isAuth };
});
