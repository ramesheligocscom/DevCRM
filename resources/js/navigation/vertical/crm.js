const menuItems = [
  { heading: 'CRM' },
  {
    title: 'User List',
    icon: { icon: 'tabler-user-circle' },
    to: 'quotation-list',
    permission: { action: 'user', subject: 'view' },
  },
  {
    title: 'Role and Permission',
    icon: { icon: 'tabler-user-circle' },
    to: 'role-list',
    permission: { action: 'role', subject: 'view' },
    meta: [{ action: 'role', subject: 'view' }]
  },
  {
    title: 'Leads',
    icon: { icon: 'tabler-circle-check' },
    to: 'lead-list',
    permission: { action: 'leads', subject: 'view' },
  },
  {
    title: 'Clients',
    icon: { icon: 'tabler-user-circle' },
    to: 'clients-list',
    permission: { action: 'client', subject: 'view' },
  },
  {
    title: 'Contracts',
    icon: { icon: 'tabler-user-circle' },
    to: 'contract-list',
    permission: { action: 'contract', subject: 'view' },
  },
  {
    title: 'Quotations',
    icon: { icon: 'tabler-user-circle' },
    to: 'quotation-list',
    permission: { action: 'quotation', subject: 'view' },
  },
  {
    title: 'Invoices',
    icon: { icon: 'tabler-user-circle' },
    to: 'invoice-list',
    permission: { action: 'invoice', subject: 'view' },
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
