@extends('layouts.app')

@section('title',' - Room'.$id)

@section('content')
    <div id="app" class="full-height bg-dark d-flex flex-column align-items-center justify-content-center">

        <ul class="text-light" style="position:fixed;right:5rem;top:5rem">
            <h4>Online</h4>
            <li v-for="user in users" v-text="user.name"></li>
        </ul>

        <div id="chat" class="text-light" >
            <p v-for="msg in msgs">
            @{{msg.name}}: <span style="font-size:1.5rem">@{{msg.body}}</span> Created: @{{msg.created_at}}
            </p>                     
        </div>

        <form @submit.prevent="sendMessage" class="w-50" class="form-group">
            {{ csrf_field() }}
            <input type="text" name="message" class="form-control" v-model="msg" placeholder="Message..." autocomplete="off">
        </form>

        
    </div>



@endsection

@section('scripts')

<script>
    const app = new Vue({
        el: '#app',
        data: {
            msgs : {},
            msg: "",
            users: []
        },
        mounted() {
            this.getMessages();
            this.listen();
        },
        methods: {
            getMessages(){
                axios.get('/room/'+{{$id}}+'/msgs')
                .then((res) => {
                    this.msgs = res.data
                    // console.log(this.msgs)
                })
                .catch(err => {
                    console.log(err)
                })
            },
            sendMessage(){
                axios.post('/room/'+{{$id}},{
                    body: this.msg
                })
                .then(res => {
                    this.msgs.push(res.data);
                    document.getElementById('chat').scrollTo(0, document.getElementById('chat').scrollHeight);
                    this.msg = "";
                })
                .catch(err => {
                    console.log(err);
                })
            },
            listen() {
                Echo.join('room.'+{{$id}})
                    .here(users => {
                        this.users = users;
                    })
                    .joining(user => {
                        this.users.push(user);
                    })
                    .leaving(user => {
                        this.users.splice(this.users.indexOf(user),1);
                    })
                    .listen('NewMessage', (message) =>{
                        this.msgs.push(message);
                        // console.log(message);
                        document.getElementById('chat').scrollTo(0, document.getElementById('chat').scrollHeight);
                    })
            }
        }
    });
</script>
@endsection