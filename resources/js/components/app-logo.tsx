import AppLogoIcon from './app-logo-icon';

export default function AppLogo() {
    return (
        <>
            <div className="flex aspect-square size-8 items-center justify-center rounded-md bg-primary text-primary-foreground">
                <AppLogoIcon className="size-5 stroke-current" />
            </div>
            <div className="ml-2 grid flex-1 text-left">
                <span className="truncate text-lg font-bold tracking-tight">
                    Homedine
                </span>
            </div>
        </>
    );
}
