FROM python:3.8
WORKDIR /usr/src/app
COPY . .
RUN pip install --no-cache-dir -r requirements.txt
CMD ["flask", "--app", "app" , "run", "--host", "0.0.0.0", "--debug"]
EXPOSE 5000