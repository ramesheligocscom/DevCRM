# CRM Project

## Vuexy Theme Integration
We are using the Vuexy theme for the admin dashboard.
- **Theme URL**: [Vuexy Vue.js Admin Template](https://demos.pixinvent.com/vuexy-vuejs-admin-template/demo-1/dashboards/crm)
- **Upgrade**: Upgrading Laravel from version 11 to 12 within the theme.

## Laravel Modules
We are integrating Laravel Modules to manage different application modules (e.g., Leads, Invoices, etc.).
- **Reference**: [Laravel Modules Documentation](https://laravelmodules.com/)

## Configuration Changes


## Custom Integration (Vuexy + Laravel Modules)

## Router Configuration

### Importing Dynamic Modular Router File
# File: resources/js/plugins/1.router/index.js
# Step 1: Dynamically import all module router files
const moduleRoutes = import.meta.glob('@modules/*/resources/assets/js/router/index.{js,ts}', { eager: true })

# Step 2: Merge all routes (dynamic + additionalRoutes)
const mergedModuleRoutes = Object.values(moduleRoutes).flatMap(mod => {
  const r = mod.default || []
  return Array.isArray(r) ? r : [r]
})

## USE ...mergedRoutes

### Vite Configuration
File: vite.config.js

### Adding Alias for Importing All Module Pages
'@modules': fileURLToPath(new URL('./Modules', import.meta.url)),

### Composer Configuration
Modify `composer.json` to include the module-specific `composer.json` files:

#### File: `composer.json`
```json
"extra": {
    "merge-plugin": {
        "include": [
            "Modules/*/composer.json"
        ]
    }
}
