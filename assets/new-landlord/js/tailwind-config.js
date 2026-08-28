tailwind.config = {
  theme: {
    extend: {
      colors: {
        // Dynamic colors from admin Color Settings (CSS variables set in color-font-variable.blade.php)
        primary: 'var(--main-color-one, #92E721)',
        secondary: 'var(--heading-color, #252C38)',
        heading: 'var(--main-color-two, #92E721)',
        'base-100': 'var(--light-color, #ECEDEF)',
        'base-200': 'var(--heading-color, #252C38)',
        'borderCS': '#D1D5D9',
        'subTitle': 'rgba(235,246,73,0.20)',
        'sub2Title': '#374253',
        'sectionC': '#0C4D54',
        'rise': '#FFFAEE',
        'quate': '#374253',

        // dashborad all color
        'aside': '#0C4D54'
      },

      teal: {
        800: '#0F766E',   // closest match to dark teal in many SaaS tables
      },

      fontFamily: {
        inter: ["Inter", "sans-serif"],  // legacy kept for compatibility
        shorooq: ["Shorooq", "Inter", "sans-serif"],

        urbanist: ["Urbanist", "sans-serif"],
      },








      screens: {
        'mobilexl': '576px',
        'mobile': '475px',
        'mobile-sm': '425px',
      },

    },
  }
}
