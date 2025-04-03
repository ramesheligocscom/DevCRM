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

### Importing Modular Router File
import leadsRoutes from '@modules/Leads/resources/assets/js/router';

### Merging Routes
const routes2 = [...routes, ...leadsRoutes];
extend(routes2);
File: resources/js/plugins/1.router/index.js

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
