import type { InertiaSharedData } from "@/types/generated/App/Modules/Support/Data";

declare module "react" {
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module "@inertiajs/core" {
    export interface InertiaConfig {
        sharedPageProps: InertiaSharedData & {
            [key: string]: unknown;
        };
    }
}
