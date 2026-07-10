export default {
  title: 'URLC System',
  description: 'Developer Handbook & Technical Documentation',
  themeConfig: {
    logo: null,
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Getting Started', link: '/getting-started' },
      { text: 'Architecture', link: '/architecture/overview' },
      { text: 'Database', link: '/database/schema' }
    ],
    sidebar: [
      {
        text: 'Introduction',
        items: [
          { text: 'Overview', link: '/' },
          { text: 'Getting Started', link: '/getting-started' }
        ]
      },
      {
        text: 'Architecture & Design',
        items: [
          { text: 'System Architecture', link: '/architecture/overview' },
          { text: 'Key File Map', link: '/architecture/folder-structure' }
        ]
      },
      {
        text: 'Database & Security',
        items: [
          { text: 'Database Schema', link: '/database/schema' },
          { text: 'Row Level Security (RLS)', link: '/database/security' }
        ]
      },
      {
        text: 'Deployment & Operations',
        items: [
          { text: 'CI/CD & DevOps', link: '/deployment/devops' },
          { text: 'Hosting & Storage', link: '/deployment/hosting' }
        ]
      }
    ],
    socialLinks: [
      { icon: 'github', link: 'https://github.com/drey-yah/urlc-system' }
    ],
    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2026 URLC System Development Team'
    }
  }
}
