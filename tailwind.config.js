/** @type {import(tailwindcss).Config} */
module.exports = {
  content: ["./resources/views/**/*.blade.php","./app/View/Components/**/*.php","./resources/js/**/*.js"],
  theme: { extend: { colors: { brand: "var(--brand)" } } },
  plugins: []
};
