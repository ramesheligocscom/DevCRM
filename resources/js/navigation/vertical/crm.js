export default [
    { heading: 'CRM' },
    {
      title: 'Leads',
      icon: { icon: 'tabler-circle-check' },
      to: 'lead-list', // ✅ Use exact name from router
    },
    {
      title: 'Clients',
      icon: { icon: 'tabler-user-circle' },
      to: 'clients-list', // ✅ Use exact name from router
    },
    {
      title: 'Role and Permission',
      icon: { icon: 'tabler-user-circle' },
      to: 'role-list',  
    },
    {
      title: 'Contracts',
      icon: { icon: 'tabler-user-circle' },
      to: 'contract-list', // ✅ Use exact name from router
    }
  ]
  