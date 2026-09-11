tailwind.config = {
  darkMode: 'class', // Enables dark mode based on the 'dark' class
  theme: {
    extend: {
      colors: {
        // Theme-aware dynamic colors for Admin Panel
        theme: {
            main: 'var(--bg-main)',
            sidebar: 'var(--bg-sidebar)',
            card: 'var(--bg-card)',
            accent: 'var(--bg-accent)',
            input: 'var(--bg-input)',
        },
        primary: {
            DEFAULT: 'var(--color-primary)',
            hover: 'var(--color-primary-hover)',
            soft: 'var(--color-primary-soft)',
            'soft-hover': 'var(--color-primary-soft-hover)',
            text: 'var(--color-text-brand)',
        },
        'text-theme': {
            main: 'var(--text-main)',
            muted: 'var(--text-muted)',
            heading: 'var(--text-heading)',
            light: 'var(--text-light)',
            accent: 'var(--text-accent)',
        },
        'border-theme': {
            main: 'var(--border-main)',
            hover: 'var(--border-hover)',
            light: 'var(--border-light)',
        },

        // Legacy/Existing colors (keep for compatibility if needed)
        'base-100': 'var(--light-color, #ECEDEF)',
        'base-200': 'var(--heading-color, #252C38)',
        'borderCS': '#D1D5D9',
        'subTitle': 'rgba(235,246,73,0.20)',
        'aside': '#0C4D54'
      },
      fontFamily: {
        inter: ["Inter", "sans-serif"],
        urbanist: ["Urbanist", "sans-serif"],
      },
      screens: {
        'mobilexl': '576px',
        'mobile': '475px',
        'mobile-sm': '425px',
        'sm': '640px',
        'md': '768px',
        'lg': '1024px',
        'xl': '1280px',
        '2xl': '1536px',
      },
      boxShadow: {
        'theme': 'var(--shadow-main)',
      }
    },
  }
}
