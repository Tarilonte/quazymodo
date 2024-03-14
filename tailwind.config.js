module.exports = {
  content: [
    "./templates/**/*",
    "./components/**/*",
    "./controllers/**/*",
    "./data/**/*",
    "./core/**/*",
  ],
  theme: {
    extend: {
      colors: {
        tomato:'tomato',
      },
      backdropBlur: { xs: '2px' },
      boxShadow: {
        'navbar': '0 10px 40px -5px rgba(0, 0, 0, 0.3), 0 25px 40px -12px rgba(0, 0, 0, 0.3)',
      },
      fontFamily: {
        sans: ["Noto Sans", "sans-serif"],
        rubik: ["Rubik", "sans-serif"],
        cutive: ["Cutive", "sans-serif"],
        heading: ['Rubik', 'serif'],
      },
      typography: {
        DEFAULT: {
          // As configurações abaixo extendem a configuração padrão do typography: https://github.com/tailwindlabs/tailwindcss-typography/blob/master/src/styles.js
          css: {
            maxWidth: '70ch',
            h1: {fontWeight: "600" },
            h2: {fontWeight: "600" },
          }
        }
      }
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
    require("daisyui")
  ],
  daisyui: {
    themes: [
      {
        light: {
          ...require("daisyui/src/theming/themes")["fantasy"],
          'primary' : '#d8e6e8',
          'primary-focus' : '#b4d2cf',
          'primary-content' : '#3d646b',
          'secondary' : '#f4f1bb',
          'secondary-focus' : '#d5d3aa',
          'secondary-content' : '#65634e',
          'accent' : '#ff6347',
          'accent-focus' : '#a44332',
          'accent-content' : '#fae5db',
          'neutral' : '#5e6268',
          'neutral-focus' : '#2a2e37',
          'neutral-content' : '#dadbdd',
          'base-100' : '#fffcfc',
          'base-200' : '#f4f0f0',
          'base-300' : '#e9e2e2',
          'base-content' : '#1e2734',
        },
      },
      {
        dark: {
          ...require("daisyui/src/theming/themes")["business"],
          'primary' : '#3d646b',
          'primary-focus' : '#b4d2cf',
          'primary-content' : '#d8e6e8',
          'secondary' : '#65634e',
          'secondary-focus' : '#d5d3aa',
          'secondary-content' : '#f4f1bb',
          'accent' : '#ff6347',
          'accent-focus' : '#a44332',
          'accent-content' : '#fae5db',
        },
      },
    ],
  },
}