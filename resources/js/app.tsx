import "../css/app.css"

import { createInertiaApp, router } from "@inertiajs/react"
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers"
import { createRoot } from "react-dom/client"
import { ThemeProvider } from "./components/theme-provider"
import { initializeTheme } from "./hooks/use-appearance"

const appName = import.meta.env.VITE_APP_NAME || "Laravel"

// Configure Inertia to include CSRF token in requests
router.on("before", (event) => {
    const token = document.head.querySelector('meta[name="csrf-token"]')
    if (token && event.detail.visit.method !== "get") {
        event.detail.visit.headers = {
            ...event.detail.visit.headers,
            "X-CSRF-TOKEN": token.getAttribute("content") ?? "",
        }
    }
})

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.tsx`,
            import.meta.glob("./pages/**/*.tsx"),
        ),
    setup({ el, App, props }) {
        const root = createRoot(el)

        root.render(
            <ThemeProvider>
                <App {...props} />
            </ThemeProvider>,
        )
    },
    progress: {
        color: "#4B5563",
    },
})

// This will set light / dark mode on load...
initializeTheme()
