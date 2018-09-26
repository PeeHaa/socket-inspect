const connection = new WebSocket('ws://127.0.0.1:8080/live');

connection.addEventListener('message', function(message) {
    handleNewMessage(JSON.parse(message.data));
});

connection.addEventListener('open', function() {
    document.querySelector('li.new form').addEventListener('submit', function(e) {
        e.preventDefault();

        connection.send(JSON.stringify({
            proxy_address: document.querySelector('li.new form input[name="proxy_address"]').value,
            proxy_encrypted: false,
            server_address: document.querySelector('li.new form input[name="server_address"]').value,
            server_encrypted: document.querySelector('li.new form input[name="server_encrypted"]').checked
        }));
    });
});

function handleNewMessage(message) {
    const container = getConsole(message.server);
    const list      = container.querySelector('ul');
    const item      = document.createElement('li');
    const pre       = document.createElement('pre');

    item.classList.add('client');

    pre.textContent = message.message;

    const label = document.createElement('h2');

    label.classList.add('info');

    if (message.initiator === 'proxy') {
        item.classList.add('proxy');

        label.textContent = 'Proxy';
    } else if (message.initiator === 'server') {
        item.classList.add('server');

        label.textContent = 'Server';
    } else {
        item.classList.add('client');

        label.textContent = 'Client';
    }

    item.appendChild(label);

    item.appendChild(pre);

    list.appendChild(item);

    const listItem = document.querySelector('aside li[data-server="' + message.server + '"]');

    listItem.classList.add('newMessage');

    setTimeout(function() {
        listItem.classList.remove('newMessage');
    }, 1000);

    list.scrollTop = list.scrollHeight;
}

function getConsole(server) {
    if (document.querySelector('.console[data-server="' + server + '"]')) {
        return document.querySelector('.console[data-server="' + server + '"]');
    }

    createListItem(server);

    const container = document.createElement('div');
    const header    = document.createElement('div');
    const h1        = document.createElement('h1');
    const list      = document.createElement('ul');

    h1.textContent = server;

    header.classList.add('header');

    header.appendChild(h1);

    container.classList.add('console');

    container.appendChild(header);
    container.appendChild(list);

    container.dataset.server = server;

    document.querySelector('main').appendChild(container);

    document.querySelectorAll('main .console').forEach(function(item) {
        item.classList.remove('active');
    });

    container.classList.add('active');

    return container;
}

function createListItem(server) {
    const list = document.querySelector('aside ul');
    const item = document.createElement('li');

    item.classList.add('server', 'active');

    item.textContent = server;

    item.dataset.server = server;

    document.querySelectorAll('aside ul li.server').forEach(function(serverItem) {
        serverItem.classList.remove('active');
    });

    item.classList.add('active');

    list.appendChild(item);
}

document.querySelector('aside ul').addEventListener('click', function(e) {
    const target = e.target;

    if (target.tagName !== 'LI' || !target.classList.contains('server') || target.classList.contains('active')) {
        return;
    }

    document.querySelectorAll('aside ul li.active').forEach(function(item) {
        item.classList.remove('active');
    });

    target.classList.add('active');

    if (document.querySelector('.console.active')) {
        document.querySelector('.console.active').classList.remove('active');
    }

    document.querySelector('.console[data-server="' + target.textContent + '"]').classList.add('active');
});
