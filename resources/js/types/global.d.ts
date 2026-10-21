import type { SharedData } from "@/types/generated/App/Modules/Support/Http/Data";

declare module "react" {
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module "@inertiajs/core" {
    export interface InertiaConfig {
        sharedPageProps: SharedData & {
            [key: string]: unknown;
        };
    }
}
