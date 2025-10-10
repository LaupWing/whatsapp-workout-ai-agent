import { createContext, useContext, ReactNode } from "react"
import { useAppearance, Appearance } from "@/hooks/use-appearance"

interface ThemeProviderContextType {
    appearance: Appearance
    setAppearance: (appearance: Appearance) => void
}

const ThemeProviderContext = createContext<
    ThemeProviderContextType | undefined
>(undefined)

interface ThemeProviderProps {
    children: ReactNode
}

export function ThemeProvider({ children }: ThemeProviderProps) {
    const { appearance, updateAppearance } = useAppearance()

    return (
        <ThemeProviderContext.Provider
            value={{
                appearance,
                setAppearance: updateAppearance,
            }}
        >
            {children}
        </ThemeProviderContext.Provider>
    )
}

export function useTheme() {
    const context = useContext(ThemeProviderContext)
    if (context === undefined) {
        throw new Error("useTheme must be used within a ThemeProvider")
    }
    return context
}
