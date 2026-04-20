export type Course = {
    id: number;
    title: string;
    description: string | null;
    duration_minutes: number | null;
    enrolled_students_count?: number;
    modules?: Module[];
};

export type CourseContent = {
    id: number;
    type: string;
    body: string;
    order?: number;
};

export type Topic = {
    id: number;
    title: string;
    contents: CourseContent[];
    order?: number;
};

export type Lesson = {
    id: number;
    title: string;
    topics: Topic[];
    order?: number;
};

export type Module = {
    id: number;
    title: string;
    lessons: Lesson[];
    order?: number;
};

export type Enrollment = Course & {
    modules: Module[];
};
