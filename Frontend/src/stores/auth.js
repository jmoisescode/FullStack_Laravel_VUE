import { defineStore } from "pinia";
import axios from "@/axios";
import { ref } from "vue";
import { VueCookieNext } from "vue-cookie-next";

export const useAuthStore = defineStore("auth", () => {
  const token = ref(VueCookieNext.getCookie("token") || null);
  const user = ref(JSON.parse(VueCookieNext.getCookie("user")) || null);
  const isAuthenticated = (state) => !!state.token;
  const isAdmin = (state) => state.user?.role === 'admin';
  const login = async (email, password) => {
    const { data } = await axios.post("/login", {
      email,
      password,
    });

    token.value = data.access_token;
    user.value = data.user;

    VueCookieNext.setCookie("user", JSON.stringify([user.value]));
    VueCookieNext.setCookie("token", data.access_token);

    await me();
    return data;
  };

  const register = async (email, password, name, role) => {
    const { data } = await axios.post("/register", {
      email,
      password,
      name,
      role,
    });
 
 

    return data;
  };

  const me = async () => {
    const { data } = await axios.get("/me");
    VueCookieNext.setCookie("user", JSON.stringify([data]));
    return data;
  };

  const logout = async () => {
    token.value = null;
    user.value = null;
    VueCookieNext.removeCookie("token");
    VueCookieNext.removeCookie("user");
    delete axios.defaults.headers.common["Authorization"];
  };
  return { token, user, login, register, logout, isAuthenticated, isAdmin };
});
