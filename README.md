Option 1:Email: gill@college.eduPassword: Student@123Option 2:Email: student@college.eduPassword: Student@123Student 
🛡️ Admin Login ku:Email: admin@college.eduPassword: Admin@123

cd /home/workspace/my-project/complaint-system
pkill -9 php; sleep 2
nohup php -S 0.0.0.0:8080 -t /home/workspace/my-project/complaint-system > /tmp/s.log 2>&1 &
sleep 2
cat /tmp/s.log
echo "--------------------------------"
echo "LINK READY DA:"
echo "https://805bc702ab0e-0af412bf-5300.ws6.app/proxy/8080/"
ls -lh *.php | head -20



# PHP

A minimal HTTP server written in PHP that exposes a friendly greeting and a `/ping` endpoint.

## What does this application do?

This application serves a simple web server that listens on defined port, default: `8000`.

# How to run?

You can run the application in one of the following ways:

1. Press `F5`. This will start the application in debug mode.

2. Open a terminal by going to 'View' -> 'Terminal'. Then run:
   > `php -S 0.0.0.0:8000 -t public`

This will start the application in development mode.

## Via curl command:

1. Open a terminal.
2. Type the following command:
   > `curl http://localhost:8000/api/ping`
3. Press 'Enter' to make the request.

Happy coding! 🙂
# mahendrasalemcampuscsec3rdyear
