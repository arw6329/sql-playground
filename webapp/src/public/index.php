<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SQL Playground</title>
        <style>
            html, body {
                margin: 0;
                display: flex;
                flex-direction: column;
            }
        </style>
    </head>
    <body>
        <header></header>
        <main>
            <query-worksheet></query-worksheet>
        </main>
        <script src="https://unpkg.com/vue"></script>
        <script src="/frontend.umd.js"></script>

        <!-- <script>
            document.querySelector('query-block').results = [{
                type: 'resultset',
                columns: [ 'col1', 'col2', 'col3', 'col4' ],
                rows: [
                    [ 1, 2, 3, 4 ],
                    [ 'a', 'a loooooooooooooong column value\nwith newline', 'b', 'abc def!' ]
                ]
            }, {
                type: 'resultset',
                columns: [ 'I am another resultset!' ],
                rows: [
                    [ 'wow!' ]
                ]
            }]
        </script> -->
    </body>
</html>