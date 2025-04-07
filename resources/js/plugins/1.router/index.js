import { setupLayouts } from 'virtual:generated-layouts';
import { createRouter, createWebHistory } from 'vue-router/auto';
import { redirects, routes } from './additional-routes';
import { setupGuards } from './guards';

// Step 1: Dynamically import all module router files
const moduleRoutes = import.meta.glob('@modules/*/resources/assets/js/router/index.{js,ts}', { eager: true })

// Step 2: Merge all routes (dynamic + additionalRoutes)
const mergedModuleRoutes = Object.values(moduleRoutes).flatMap(mod => {
  const r = mod.default || []
  return Array.isArray(r) ? r : [r]
})

const mergedRoutes = [...routes, ...mergedModuleRoutes]


function recursiveLayouts(route) {
  if (route.children) {
    for (let i = 0; i < route.children.length; i++)
      route.children[i] = recursiveLayouts(route.children[i])
    
    return route
  }
  
  return setupLayouts([route])[0]
}


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to) {
    if (to.hash)
      return { el: to.hash, behavior: 'smooth', top: 60 }
    
    return { top: 0 }
  },
  extendRoutes: pages => [
    ...redirects,
    ...[
      ...pages,
      ...mergedRoutes,
    ].map(route => recursiveLayouts(route)),
  ],
})

setupGuards(router)
export { router };
export default function (app) {
  app.use(router)
}
