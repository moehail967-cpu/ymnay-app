tailwind.config = {
  theme: {
    extend: {
      colors: {
        // Dynamic colors from admin Color Settings (CSS variables set in color-font-variable.blade.php)
        primary: 'var(--main-color-one, #F04751)',
        secondary: 'var(--heading-color, #333333)',
        heading: 'var(--main-color-two, #FF805D)',
        'base-100': 'var(--light-color, #ECEDEF)',
        'base-200': 'var(--heading-color, #333333)',
        'borderCS': '#D1D5D9',
        'subTitle': 'var(--secondary-color, #F7A3A8)',
        'sub2Title': 'var(--heading-color, #374253)',
        'sectionC': 'var(--main-color-one, #F04751)',
        'rise': 'var(--section-bg-2, #FFF6EE)',
        'quate': 'var(--heading-color, #374253)',

        // Admin panel sidebar — uses admin CSS variable system
        'aside': 'var(--color-bg-sidebar, #E7F7F7)'
      },

      teal: {
        800: '#0F766E',   // closest match to dark teal in many SaaS tables
      },

      fontFamily: {
        // Dynamic fonts from admin Typography Settings (CSS variables set in color-font-variable.blade.php)
        inter: ["var(--body-font, 'Inter')", "sans-serif"],

        urbanist: ["var(--heading-font, 'Urbanist')", "sans-serif"],
      },








      screens: {
        'mobilexl': '576px',
        'mobile': '475px',
        'mobile-sm': '425px',
      },

    },
  }
}
