export const panelDetails = defineStore('panelDetails', () => {
  const userDetails = ref('');
  const permissionList = ref("");

  const getUserDetails = async () => {
    const { data } = await useApi('/profile');
    userDetails.value = data.value.data;
  }

  const getUserPermission = async () => {
    const { data } = await useApi("/role/user-permission");
    permissionList.value = data.value.data;
    const abilityList = permissionList.value.map((p) => ({
      action: p.action,
      subject: p.slug,
    }));
    localStorage.setItem("permission_list", JSON.stringify(abilityList));
  };

  return { userDetails, getUserDetails, permissionList, getUserPermission };
});
