import React from 'react';
import { Head } from '@inertiajs/react';

export default function Index({ users }) {
    return (
        <>
            <Head title="Users" />

            <main>
                <h1>Users</h1>

                {users.length === 0 ? (
                    <p>No users found.</p>
                ) : (
                    <ul>
                        {users.map((user) => (
                            <li key={user.id}>
                                {user.name} ({user.email})
                            </li>
                        ))}
                    </ul>
                )}
            </main>
        </>
    );
}
