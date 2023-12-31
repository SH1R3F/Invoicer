export default [
  {
    title: 'Dashboard',
    to: { name: 'index' },
    icon: { icon: 'tabler-smart-home' },
  },
  {
    title: 'Roles & Users',
    icon: { icon: 'tabler-settings-cog' },
    children: [
      {
        title: 'Roles',
        to: 'roles',
        action: 'Read',
        subject: 'role',
      },
      {
        title: 'Users',
        to: 'users',
        action: 'Read',
        subject: 'user',
      },
    ],
  },
  {
    title: 'Categories',
    to: { name: 'categories' },
    icon: { icon: 'tabler-category' },
    action: 'Read',
    subject: 'category',
  },
  {
    title: 'Products',
    to: { name: 'products' },
    icon: { icon: 'tabler-package' },
    action: 'Read',
    subject: 'product',
  },
  {
    title: 'Taxes',
    to: { name: 'taxes' },
    icon: { icon: 'tabler-receipt-tax' },
    action: 'Read',
    subject: 'tax',
  },
]
