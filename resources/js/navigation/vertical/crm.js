const menuItems = [
  { heading: 'CRM' },
  {
    title: 'Dashboard',
    icon: { icon: 'tabler-smart-home' },
    to: 'dashboard',
    permission: { action: 'dashboard', subject: 'view' },
  },
  {
    title: 'User List',
    icon: { icon: 'tabler-user-circle' },
    to: 'user-list',
    permission: { action: 'user', subject: 'view' },
    otherRouteList: ["user-view-id"]
  },
  {
    title: 'Role and Permission',
    icon: { icon: 'tabler-user-circle' },
    to: 'role-list',
    permission: { action: 'role', subject: 'view' },
    otherRouteList: ["role-edit"]
  },
  {
    title: 'Leads',
    icon: { icon: 'tabler-user' },
    to: 'lead-list',
    permission: { action: 'leads', subject: 'view' },
    otherRouteList: ["lead-details-id"]
  },
  {
    title: 'Clients',
    icon: { icon: 'tabler-users' },
    to: 'clients-list',
    permission: { action: 'client', subject: 'view' },
    otherRouteList: ["client-details-id"]
  },
  {
    title: 'Contracts',
    icon: { icon: 'tabler-file-stack' },
    to: 'contract-list',
    permission: { action: 'contract', subject: 'view' },
    otherRouteList: ["contract-details-id", "contract-edit", "contract-create"]
  },
  {
    title: 'Quotations',
    icon: { icon: 'tabler-brand-asana' },
    to: 'quotation-list',
    permission: { action: 'quotation', subject: 'view' },
    otherRouteList: ["quotation-details-id", "quotation-edit", "quotation-create"]
  },
  {
    title: 'Invoices',
    icon: { icon: 'tabler-user-circle' },
    to: 'invoice-list',
    permission: { action: 'invoice', subject: 'view' },
    otherRouteList: ["invoice-details-id", "invoice-edit", "invoice-create"]
  },
  {
    title: 'Product/Services',
    icon: { icon: 'tabler-building-warehouse' },
    to: 'product-service-list',
    permission: { action: 'invoice', subject: 'view' },
  },
  {
    title: 'Setting',
    icon: { icon: 'tabler-settings' },
    to: 'settings',
    permission: null,
    extra_permission: [
      { action: "generalSetting", subject: "view" },
      { action: "status", subject: "view" },
    ],
  },
];

const list = () => {
  const hasPermission = (ability, action, subject) => {
    return ability.some(val => val.action === action && val.subject === subject)
  }

  const permission = localStorage.getItem('permission_list') || null
  const permission_list = permission ? JSON.parse(permission) : []

  const checkItemPermission = item => {
    // Always show headers
    if (item.heading) return true

    // If no permission or extra_permission, allow by default
    if (!item.permission && !item.extra_permission) return true

    // Check direct permission
    if (item.permission) {
      const { action, subject } = item.permission
      if (hasPermission(permission_list, action, subject)) {
        return true
      }
    }

    // Check extra permissions (if any match)
    if (Array.isArray(item.extra_permission)) {
      return item.extra_permission.some(({ action, subject }) =>
        hasPermission(permission_list, action, subject),
      )
    }

    return false
  }

  const filterMenu = items => {
    return items
      .map(item => {
        // Recursively handle children
        if (item.children) {
          const children = filterMenu(item.children)
          return children.length ? { ...item, children } : null
        }

        // Check permission
        return checkItemPermission(item) ? item : null
      })
      .filter(Boolean)
  }

  return filterMenu(menuItems)
}

export default list()
