import { createMongoAbility } from '@casl/ability';
import { abilitiesPlugin } from '@casl/vue';

export default function (app) {
  let permission = localStorage.getItem('permission_list') ?? null;
  const permission_list = permission ? JSON.parse(permission) : [];
  const initialAbility = createMongoAbility(permission_list)

  app.use(abilitiesPlugin, initialAbility, {
    useGlobalProperties: true,
  })
}
