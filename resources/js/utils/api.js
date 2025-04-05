import { ofetch } from "ofetch";
import { toast } from "vue3-toastify";

export const $api = ofetch.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || "/api",

  async onRequest({ options }) {
    const accessToken = useCookie("accessToken").value;
    if (accessToken) {
      options.headers = {
        ...options.headers,
        Accept: "application/json",
        Authorization: `Bearer ${accessToken}`,
      };
    }
  },

  async onResponseError({ response }) {
    const errorMessage = response?._data?.message || "";
    if (errorMessage.toLowerCase().includes("unauthenticated")) {
      toast.error("Session expired. Please log in again.");

      // Get user ID before clearing storage
      const userId = useCookie("userData").value?.id || localStorage.getItem("user_id");
      // Call the API to log logout event
      if (userId) {
        try {
          await ofetch("/api/log-unauthenticated-access", {
            method: "POST",
            body: { user_id: userId },
          });
        } catch (error) {
          console.error("Failed to log unauthenticated event:", error);
        }
      }
      // Clear cookies & session data
      useCookie("userAbilityRules").value = null;
      useCookie("userData").value = null;
      useCookie("accessToken").value = null;

      // Clear all stored user data
      sessionStorage.clear();
      localStorage.clear();
      window.location.href = "/login";
    }

    return Promise.reject(response);
  },
});

